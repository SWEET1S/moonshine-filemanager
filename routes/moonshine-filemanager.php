<?php

use Illuminate\Support\Facades\Route;

$middlewares = collect(config('moonshine.route.middleware'))
    ->reject(static fn($middleware): bool => $middleware === 'web')
    ->toArray();

Route::middleware($middlewares)->group(function () {

    Route::middleware([config('moonshine.auth.middleware'), 'web'])->group(function () {

        Route::group(['prefix' => 'filemanager'], function () {
            \UniSharp\LaravelFilemanager\Lfm::routes();
        });

    });
});
