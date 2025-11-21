<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ElementController;

// ==========================
// トップページ（ダッシュボード）
// ==========================
Route::get('home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : view('landing');
});

// ==========================
// 認証ルート
// ==========================
Auth::routes();

// ==========================
// ログイン必須ルート
// ==========================
Route::middleware('auth')->group(function () {

    // ==========================
    // Project関連
    // ==========================
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/check-name', [ProjectController::class, 'checkName'])->name('checkName');

        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    });

    // ==========================
    // Element関連
    // ==========================
    Route::prefix('elements')->name('elements.')->group(function () {
        Route::get('/', [ElementController::class, 'index'])->name('index');
        Route::post('/', [ElementController::class, 'store'])->name('store');
        Route::get('/check', [ElementController::class, 'check'])->name('check');

        // 追加要素
        Route::get('/additional/{projectId}', [ElementController::class, 'createAdditional'])
            ->name('create_additional');
        Route::post('/additional', [ElementController::class, 'storeAdditional'])
            ->name('store_additional');

        // 編集・削除
        Route::get('/{element}/edit', [ElementController::class, 'edit'])->name('edit');
        Route::put('/{element}', [ElementController::class, 'update'])->name('update');
        Route::delete('/{element}', [ElementController::class, 'destroy'])->name('destroy');
    });
});
