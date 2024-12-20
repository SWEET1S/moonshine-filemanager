<?php

namespace Sweet1s\MoonshineFileManager;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\File;

class FileManager extends File
{
    protected string $view = 'moonshine-filemanager::fields.fileManager';
    protected bool $multiple = true;
    protected FileManagerTypeEnum $typeOfFileManager = FileManagerTypeEnum::Image;
    protected string $title = 'File Manager';

    public function __construct(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
    {
        parent::__construct($label, $column, $formatted);
        $this->setAttribute('multiple', true);
    }

    /**
     * This method is used to set the type of file manager. Can be Image or File. Default is Image
     *
     * @param FileManagerTypeEnum $type
     * @return $this
     */
    public function typeOfFileManager(FileManagerTypeEnum $type): static
    {
        $this->typeOfFileManager = $type;
        return $this;
    }

    public function getTypeOfFileManager(): FileManagerTypeEnum
    {
        return $this->typeOfFileManager;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function store(UploadedFile|string $file): string
    {
        if (is_string($file)) {
            $initPath = str_replace(config('app.url') . '/storage/', '', $file);
            $path = Storage::disk('public')->path($initPath);
            if (!file_exists($path)) {
                Log::error("File not found: $path");
                throw new FieldException("File not found: $path");
            }

            $file = new UploadedFile($path, basename($file));

            $extension = $file->extension();

            throw_if(
                !$this->isAllowedExtension($extension),
                new FieldException("$extension not allowed")
            );

            return $initPath;
        }

        throw new FieldException('Invalid file');
    }

    protected function resolveOnApply(): Closure
    {
        return function ($item) {
            $requestValue = $this->requestValue();

            $oldValues = request()
                ->collect($this->hiddenOldValuesKey());

            data_forget($item, 'hidden_' . $this->column());

            $saveValue = $this->isMultiple() ? $oldValues : $oldValues->first();

            if ($requestValue !== false) {
                if ($this->isMultiple()) {
                    $paths = [];

                    $requestValue = explode(',', $requestValue[0]);

                    foreach ($requestValue as $file) {
                        if ($file === '') {
                            continue;
                        }

                        $paths[] = $this->store($file);
                    }

                    $saveValue = $saveValue->merge($paths)
                        ->values()
                        ->unique()
                        ->toArray();
                } else {
                    $saveValue = $this->store($requestValue);
                }
            }
            return data_set($item, $this->column(), $saveValue);
        };
    }

    /**
     * @throws FieldException
     */
    public function multiple(Closure|bool|null $condition = null): static
    {
        throw new FieldException('FileManager field cannot be single');
    }

    /**
     * @throws FieldException
     */
    public function disk(string $disk): static
    {
        throw new FieldException('To change disk use config/lfm.php');
    }

    /**
     * @throws FieldException
     */
    public function dir(string $dir): static
    {
        throw new FieldException('FileManager field cannot have a dir');
    }

    /**
     * @throws FieldException
     */
    public function enableDeleteDir(): static
    {
        throw new FieldException('FileManager field cannot have a dir');
    }

    /**
     * @throws FieldException
     */
    public function keepOriginalFileName(): static
    {
        throw new FieldException('FileManager field cannot keep original file name. You can change the names in the file manager');
    }

    /**
     * @throws FieldException
     */
    public function customName(Closure $name): static
    {
        throw new FieldException('FileManager field cannot have a custom name');
    }
}
