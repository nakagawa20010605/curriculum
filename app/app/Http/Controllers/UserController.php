<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * ユーザーのプロフィールと投稿一覧を表示する
     *
     * @param  \App\Models\User  $user 表示するユーザーモデル
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // $user モデルは、web.phpのルート設定で自動的に注入（Implicit Model Binding）される
        // リレーション名が 'services' であることを前提に投稿を取得
        $services = $user->services()->get();

        // いいね一覧
        $likedServices = $user->likedServices()->with('user')->latest()->get();

        return view('users.show', compact('user', 'services', 'likedServices'));
    }

    /**
     * ユーザー情報編集フォームを表示する
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // 認証済みのユーザー情報のみを渡す
        return view('users.edit', [
            'user' => Auth::user(),
        ]);
    }
    
    /**
     * ユーザーのプロフィール情報（名前、メールアドレス、アイコン画像）を更新する
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileInformation(Request $request)
    {
        $user = $request->user(); // 認証済みユーザーを取得

        // バリデーションルール
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            // 'image' はファイルまたはnullを許容。削除リクエストがある場合は検証をスキップ。
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], 
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // ★ 1. 画像削除の処理 (delete_image が '1' の場合) ★
        if ($request->input('delete_image') == '1' && $user->image) {
            // 既存の画像をストレージから削除
            Storage::disk('public')->delete($user->image);
            // データベースの 'image' カラムを NULL に設定
            $data['image'] = null;
        } 
        // ★ 2. 新しい画像ファイルの処理 ★
        elseif ($request->hasFile('image')) {
            // 既存の画像があれば削除
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // 新しい画像を保存し、ファイルパスを取得
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }

        // データの更新
        $user->forceFill($data)->save();

        // プロフィール詳細画面 (users.show) にリダイレクト
        return redirect()->route('users.show', $user->id);
    }

    /**
     * ユーザーのパスワードを更新する
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user(); // 認証済みユーザーを取得

        // 1. 現在のパスワードの検証
        if (! Hash::check($request->current_password, $user->password)) {
            // パスワードが一致しない場合はバリデーション例外をスロー
            throw ValidationException::withMessages([
                'current_password' => ['現在のパスワードが正しくありません。'],
            ]);
        }
        
        // 2. 新しいパスワードのバリデーション
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'], // confirmedは password_confirmation との一致をチェック
        ]);

        // 3. パスワードのハッシュ化と更新
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // プロフィール詳細画面 (users.show) にリダイレクト
        return redirect()->route('users.show', $user->id);
    }
}