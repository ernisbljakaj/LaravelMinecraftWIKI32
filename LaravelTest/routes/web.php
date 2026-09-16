<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\WikiController;
use Illuminate\Support\Facades\Route;

// Must be declared before the slug route to avoid conflicts.
Route::middleware('auth')->group(function () {
    Route::get('/server/submit', [ServerController::class, 'create'])->name('servers.create');
    Route::post('/server/submit', [ServerController::class, 'store'])->name('servers.store');

    Route::post('/server/{server}/favorite', [FavoriteController::class, 'toggle'])->name('servers.favorite');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/', [ServerController::class, 'index'])->name('home');

Route::get('/server/list', [ServerController::class, 'index'])->name('servers.index');
Route::get('/server/{server:slug}', [ServerController::class, 'show'])->name('servers.show');

Route::get('/wiki', [WikiController::class, 'index'])->name('wiki.index');
Route::get('/wiki/{wikiPage:slug}', [WikiController::class, 'show'])->name('wiki.show');

require __DIR__.'/auth.php';
