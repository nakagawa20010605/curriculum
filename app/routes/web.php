<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LikeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| こちらにアプリケーションのウェブサイトルートを登録します。
|
*/

// ===================================
// 1. 認証 (Auth) 関連のルート
// ===================================
Auth::routes(); 


// ===================================
// 2. ホーム画面 (ServiceController@index)
// ===================================
// トップページと投稿一覧
Route::get('/', [ServiceController::class, 'index'])->name('home');
Route::get('/home', [ServiceController::class, 'index'])->name('index');


// ===================================
// 3. 投稿 (Service) 関連のルート 
// ===================================

// (1) 新規作成フォーム表示 (認証必須)
Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create')->middleware('auth');

// (2) 新規データ保存 (認証必須)
Route::post('/services', [ServiceController::class, 'store'])->name('services.store')->middleware('auth');

// リクエストの保存 (Service詳細画面からの依頼送信)
Route::post('/services/{service}/requests', [RequestController::class, 'store'])->name('requests.store')->middleware('auth');

// (3) 詳細表示 (認証不要)
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// (4) 編集フォーム表示 (認証必須)
Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit')->middleware('auth');

// (5) データ更新 (認証必須)
Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('services.update')->middleware('auth');

// (6) データ削除 (認証必須)
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('auth');

// いいね (Like) 機能のトグル
// いいねの追加・削除を同じエンドポイントで処理
Route::post('/services/{service}/like', [LikeController::class, 'toggle'])->name('services.like');

// ===================================
// 4. ユーザー情報 (User) 関連のルート
// ===================================

// (1) 詳細表示 (認証不要)
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// 認証が必要なユーザー関連のルート
Route::middleware(['auth'])->group(function () {
    
    // (2) プロフィール編集フォーム表示 
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    
    // (3) プロフィール情報更新 [PUT]
    Route::put('/user/profile-information', [UserController::class, 'updateProfileInformation'])
             ->name('user-profile-information.update');
             
    // (4) パスワード更新 [PUT]
    Route::put('/user/password', [UserController::class, 'updatePassword'])
             ->name('user-password.update');
    
    // (5) 違反報告 (Report) 関連のルート 
    // 1. 報告フォーム表示 [GET] (reports.create)
    Route::get('/services/{service}/report/create', [ReportController::class, 'create'])->name('reports.create');
    
    // 2. 報告の保存 [POST] (reports.store)
    Route::post('/services/{service}/report', [ReportController::class, 'store'])->name('reports.store');
});

// ===================================
// 5. リクエスト (依頼) 機能関連 (RequestController)
// ===================================
Route::middleware(['auth'])->group(function () {
    
    // 依頼一覧の表示（依頼者側・提供者側）
    Route::get('/requests', [RequestController::class, 'index'])
        ->name('requests.index');

    // 依頼詳細の表示
    Route::get('/requests/{serviceRequest}', [RequestController::class, 'show'])
        ->name('requests.show');

    // 依頼ステータスの更新 (承認/拒否/完了など)
    Route::put('/requests/{serviceRequest}', [RequestController::class, 'update'])
        ->name('requests.update');
    
    // リクエストのキャンセル (主に依頼者側から)
    Route::delete('/requests/{serviceRequest}', [RequestController::class, 'destroy'])
        ->name('requests.destroy');
});