<?php

use UniSharp\LaravelFilemanager\Lfm;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Middleware\Authenticate;

Route::middleware([
    'moonshine', Authenticate::class
])->group(function () {
    Route::group(['prefix' => 'filemanager'], function () {
        Lfm::routes();
    });
});
