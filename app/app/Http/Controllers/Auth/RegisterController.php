<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * 登録後のリダイレクト先URI
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * 新しいコントローラーインスタンスを作成します。
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 受信した登録リクエストのデータに対するバリデーターを取得します。
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // ★変更: iconをimageカラム名に変更★
            'image' => ['nullable', 'image', 'max:2048'], // 任意、画像ファイル、最大2MB
        ]);
    }

    /**
     * ユーザーモデルを作成した後、インカミング登録リクエストを処理します。
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $iconPath = null; // アイコンパスを初期化

        // ★変更: imageフィールドのファイル処理に変更★
        if (isset($data['image'])) {
            // ファイルを 'public/images' ディレクトリに保存
            // サービス画像とユーザーアイコンが混在するが、DBカラム名に合わせる
            $iconPath = $data['image']->store('images', 'public');
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'image' => $iconPath, // ★変更: imageカラムにファイルパスをDBに保存
        ]);
    }
}