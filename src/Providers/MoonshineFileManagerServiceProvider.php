<?php

namespace Sweet1s\MoonshineFileManager\Providers;

use Illuminate\Support\ServiceProvider;
use Sweet1s\MoonshineFileManager\FileManager;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineFileManager\Applies\FileManagerModelApply;

final class MoonshineFileManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/moonshine-filemanager.php');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'moonshine-filemanager');

        appliesRegister()->for(ModelResource::class)->fields()->push([
            FileManager::class => FileManagerModelApply::class,
        ]);
    }
}
