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

    // /home も / に統一
    Route::get('/home', fn() => redirect('/'));

    // ==========================
    // Project関連
    // ==========================
    Route::prefix('projects')->group(function () {

        // プロジェクト一覧
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');

        // プロジェクト作成画面
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');

        // プロジェクト作成処理（POST）
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');

        // プロジェクト詳細
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');

        // プロジェクト削除（Ajax）
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    });

    // ==========================
    // Element関連（Ajax）
    // ==========================
    Route::prefix('elements')->group(function () {

        // 要素登録
        Route::post('/', [ElementController::class, 'store'])->name('elements.store');

        // 登録済み要素一覧
        Route::get('/', [ElementController::class, 'index'])->name('elements.index');

        // 要素削除
        Route::delete('/{element}', [ElementController::class, 'destroy'])->name('elements.destroy');
    });
});
