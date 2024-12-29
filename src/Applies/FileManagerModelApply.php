<?php

declare(strict_types=1);

namespace Sweet1s\MoonshineFileManager\Applies;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Exceptions\FieldException;
use Sweet1s\MoonshineFileManager\FileManager;

/**
 * @implements ApplyContract<FileManager>
 */
final class FileManagerModelApply implements ApplyContract
{
    /**
     * @param FileManager $field
     */
    public function apply(FieldContract $field): Closure
    {
        return function (mixed $item) use ($field): mixed {
            /** @var Model $item */

            $remainingValues = $field->getRemainingValues();
            $requestValue = $field->getRequestValue() ? collect($field->getRequestValue()) : collect();

            if ($requestValue->first() === '') {
                $requestValue = collect();
            }

            data_forget($item, $field->getHiddenRemainingValuesKey());

            $paths = [];

            foreach ($requestValue as $file) {
                $paths[] = $this->store($file, $field);
            }

            $newValue = collect($paths)
                ->merge($remainingValues)
                ->values()
                ->unique()
                ->toArray();

            return data_set($item, $field->getColumn(), $newValue);
        };
    }

    /**
     * @param FileManager $field
     */
    public function store(string $file, FieldContract $field): string
    {
        $initPath = str_replace(config('app.url') . '/storage/', '', $file);
        $path = Storage::disk('public')->path($initPath);
        if (!file_exists($path)) {
            Log::error("File not found: $path");
            throw new FieldException("File not found: $path");
        }

        $file = new UploadedFile($path, basename($file));

        $extension = $file->extension();

        throw_if(
            !$field->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        return $initPath;

    }
}
