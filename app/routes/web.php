<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
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
// 3. 投稿 (Service) 関連のルート (優先順位順)
// ===================================

// (1) 新規作成フォーム表示 (固定パスなので最優先。認証必須)
Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create')->middleware('auth');

// (2) 新規データ保存 (認証必須)
Route::post('/services', [ServiceController::class, 'store'])->name('services.store')->middleware('auth');

// (3) 詳細表示 (変数パスなのでcreateの後に配置。認証不要)
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// (4) 編集フォーム表示 (認証必須)
Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit')->middleware('auth');

// (5) データ更新 (認証必須)
Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('services.update')->middleware('auth');

// (6) データ削除 (認証必須)
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('auth');

// ===================================
// 4. ユーザー情報 () 関連のルート
// ===================================

// (1) 詳細表示
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::middleware(['auth'])->group(function () {
    
    // (2) プロフィール編集フォーム表示 (profile.edit)
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    
    // (3) プロフィール情報更新 [PUT]
    // name="user-profile-information.update" は Bladeファイルで使用
    Route::put('/user/profile-information', [UserController::class, 'updateProfileInformation'])
         ->name('user-profile-information.update');
         
    // (4) パスワード更新 [PUT]
    // name="user-password.update" は Bladeファイルで使用
    Route::put('/user/password', [UserController::class, 'updatePassword'])
         ->name('user-password.update');
});