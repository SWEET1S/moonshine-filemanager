<?php

namespace Sweet1s\MoonshineFileManager\Providers;

use Illuminate\Support\ServiceProvider;

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
    }
}
