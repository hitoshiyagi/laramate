<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\GeneratorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==========================
// TOPページ & 認証ルート
// ==========================
Route::get('/', [HomeController::class, 'index'])->name('index');
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// ==========================
// Item関連
// ==========================
Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/add', [ItemController::class, 'add']);
    Route::post('/add', [ItemController::class, 'add']);
});

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
// Ajax専用（プロジェクト外から登録する用）
// ==========================
Route::post('/elements/store', [ElementController::class, 'store'])->name('elements.store');

// ==========================
// 登録した要素の一覧表示
// ==========================
Route::get('/elements', [ElementController::class, 'index'])->name('elements.index');