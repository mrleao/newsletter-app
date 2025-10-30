<?php

use App\Http\Controllers\Web\ArticleCategoryController;
use App\Http\Controllers\Web\ArticleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ArticleController::class, 'articlesPage'])->name('site.articles.page');
Route::get('/articles/{categoryId}', [ArticleController::class, 'readArticlePage'])->name('site.read.article.page');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('categories')
        ->name('categories.')
        ->group(function () {
            Route::get('/', [ArticleCategoryController::class, 'index'])->name('index');

            Route::get('/create', [ArticleCategoryController::class, 'storeView'])->name('storeView');
            Route::post('/create', [ArticleCategoryController::class, 'store'])->name('store');

            Route::get('/edit/{categoryId}', [ArticleCategoryController::class, 'updateView'])->name('updateView');
            Route::match(['put', 'patch'], '/edit/{categoryId}', [ArticleCategoryController::class, 'update'])
                ->whereNumber('categoryId')
                ->name('update');

            Route::delete('/{categoryId}', [ArticleCategoryController::class, 'destroy'])
                ->whereNumber('categoryId')
                ->name('delete');
        });

    Route::prefix('articles')
        ->name('articles.')
        ->group(function () {
            Route::get('/', [ArticleController::class, 'index'])->name('index');

            Route::get('/create', [ArticleController::class, 'storeView'])->name('storeView');
            Route::post('/create', [ArticleController::class, 'store'])->name('store');

            Route::get('/edit/{articleId}', [ArticleController::class, 'updateView'])->name('updateView');
            Route::match(['put', 'patch', 'post'], '/edit/{articleId}', [ArticleController::class, 'update'])
                ->whereNumber('articleId')
                ->name('update');

            Route::delete('/{Id}', [ArticleController::class, 'destroy'])
                ->whereNumber('articleId')
                ->name('delete');
        });
});
