<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ElementController;

// ==========================
// トップページ（ダッシュボード）
// ==========================
Route::get('/', [HomeController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// ==========================
// 認証ルート
// ==========================
Auth::routes();

// ==========================
// ログイン必須ルート
// ==========================
Route::middleware(['auth'])->group(function () {

    // ==========================
    // Project関連
    // ==========================
    Route::prefix('projects')->group(function () {

        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/check-name', [ProjectController::class, 'checkName'])->name('projects.checkName');

        // プロジェクト詳細（ワイルドカード）
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    });

    // ==========================
    // Element関連
    // ==========================
    Route::prefix('elements')->group(function () {

        Route::get('/', [ElementController::class, 'index'])->name('elements.index');
        Route::post('/', [ElementController::class, 'store'])->name('elements.store');
        Route::get('/check', [ElementController::class, 'check'])->name('elements.check');

        // 追加要素作成画面
        Route::get('/additional/{projectId}', [ElementController::class, 'createAdditional'])
            ->name('elements.create_additional');

        // 追加要素登録
        Route::post('/additional', [ElementController::class, 'storeAdditional'])
            ->name('elements.store_additional');

        // 編集
        Route::get('{element}/edit', [ElementController::class, 'edit'])->name('elements.edit');
        Route::put('{element}', [ElementController::class, 'update'])->name('elements.update');
        Route::delete('/{element}', [ElementController::class, 'destroy'])->name('elements.destroy');
    });
});
