
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
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
