<?php

namespace Sweet1s\MoonshineFileManager;

use Closure;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Exceptions\FieldException;

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

    public function getRequestValue(string|int|null $index = null): mixed
    {
        if (! \is_null(static::$requestValueResolver)) {
            return \call_user_func(static::$requestValueResolver, $index, $this->getDefaultIfExists(), $this);
        }

        $value = $this->prepareRequestValue(
            $this->getCore()->getRequest()->get(
                $this->getRequestNameDot($index),
                $this->getDefaultIfExists()
            ) ?? false
        );

        return explode(',', $value);
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

    public function viewData(): array
    {
        return [
            ...parent::viewData(),
            'title' => $this->getTitle(),
            'typeOfFileManager' => $this->getTypeOfFileManager(),
            'id' => $this->getIdentity(),
        ];
    }
}
