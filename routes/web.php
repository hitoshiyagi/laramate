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
Route::get('/', [HomeController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth'); // ログイン必須

// ==========================
// 認証ルート
// ==========================
Auth::routes();

// ==========================
// ログイン必須ルート
// ==========================
Route::middleware(['auth'])->group(function () {

    // /home にアクセスした場合も / に統一
    Route::get('/home', function () {
        return redirect('/');
    });
    // ==========================
    // Project関連
    // ==========================
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/store', [ProjectController::class, 'store'])->name('projects.store');

        // ここで固定パスのルートを先に置く
        Route::get('/list', [ProjectController::class, 'list'])->name('projects.list');

        // {project} は最後
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');

        // 要素群（プロジェクト単位）
        Route::get('{project}/elements/create', [ElementController::class, 'create'])->name('elements.create');
        Route::post('{project}/elements', [ElementController::class, 'store'])->name('elements.store.project');
    });


    // ==========================
    // Ajax用 Element登録
    // ==========================
    Route::post('/elements/store', [ElementController::class, 'store'])->name('elements.store');


    // ==========================
    // 登録済み Element一覧
    // ==========================
    Route::get('/elements', [ElementController::class, 'index'])->name('elements.index');


    // ==========================
    // 子要素削除
    // ==========================
    Route::delete('/elements/{element}', [ElementController::class, 'destroy'])
        ->name('elements.destroy');


    // ==========================
    // Item関連
    // ==========================
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('items.index');
        Route::get('/add', [ItemController::class, 'add']);
        Route::post('/add', [ItemController::class, 'add']);
    });
});
