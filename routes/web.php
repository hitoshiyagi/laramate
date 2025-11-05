<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\ItemController;

// ==========================
// トップページ（ダッシュボード）
// ==========================
Route::get('/', [GeneratorController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth'); // ログイン必須

// ==========================
// 認証ルート
// ==========================
Auth::routes();

// ==========================
// ログイン必須のルート
// ==========================
Route::middleware(['auth'])->group(function () {

    // HomeController は必要なら残す
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ==========================
    // Project関連
    // ==========================
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/store', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');

        // 要素群（プロジェクト単位）
        Route::get('{project}/elements/create', [ElementController::class, 'create'])->name('elements.create');
        Route::post('{project}/elements', [ElementController::class, 'store'])->name('elements.store.project');
    });

    // ==========================
    // Item関連
    // ==========================
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('items.index');
        Route::get('/add', [ItemController::class, 'add']);
        Route::post('/add', [ItemController::class, 'add']);
    });

    // ==========================
    // Ajax用 Element登録
    // ==========================
    Route::post('/elements/store', [ElementController::class, 'store'])->name('elements.store');

    // ==========================
    // 登録済み Element一覧
    // ==========================
    Route::get('/elements', [ElementController::class, 'index'])->name('elements.index');
});
