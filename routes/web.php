
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\GeneratorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// index(TOPページ)
Route::get('/', [GeneratorController::class, 'index'])->name('index');

// 認証ルート（ログイン・登録・パスワードリセット含む）
Auth::routes();

// ログイン後の画面
Route::get('/home', [HomeController::class, 'index'])
    ->name('home')
    ->middleware('auth'); // ←追加推奨（ログインしてない人をリダイレクト）

// item関連ルート
Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/add', [ItemController::class, 'add']);
    Route::post('/add', [ItemController::class, 'add']);
});

// project関連ルート
Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('{project}/elements/create', [ElementController::class, 'create'])->name('elements.create');
    Route::post('/{project}/elements', [ElementController::class, 'store'])->name('elements.store');
});
