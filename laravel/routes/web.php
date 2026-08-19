<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/'.config('app.locale', 'ar'));
});

Route::get('robots.txt', function () {
    return response(view('seo.robots')->render(), 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::get('sitemap_index.xml', function () {
    $path = public_path('sitemap_index.xml');
    if (! file_exists($path)) {
        Artisan::call('seo:sitemap');
    }

    return response()->file($path, ['Content-Type' => 'application/xml']);
});

Route::get('sitemap_{name}.xml', function (string $name) {
    $path = public_path("sitemap_{$name}.xml");
    abort_unless(file_exists($path), 404);

    return response()->file($path, ['Content-Type' => 'application/xml']);
})->where('name', '[a-z_0-9]+');
