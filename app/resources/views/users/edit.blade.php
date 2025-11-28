@extends('layouts.app')

@section('title', 'ユーザー情報編集')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- ===================================
               1. プロフィール情報更新セクション (アイコン画像含む)
            =================================== --}}
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-success text-white h4">
                    <i class="fas fa-user-edit mr-2"></i>プロフィール情報の更新
                </div>
                <div class="card-body p-4">
                    
                    {{-- フォームアクション: UserController@updateProfileInformation に向ける --}}
                    {{-- 【重要】ファイルアップロードのため enctype="multipart/form-data" を追加 --}}
                    <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data" id="profile-update-form">
                        @csrf
                        @method('PUT')

                        {{-- 【新規追加】画像を削除するための隠しフィールド --}}
                        <input type="hidden" name="delete_image" id="delete_image_input" value="0">


                        {{-- 0. ユーザーアイコン (image) --}}
                        <div class="form-group text-center mb-4">
                            <label for="image" class="font-weight-bold d-block">ユーザーアイコン (任意)</label>
                            
                            {{-- 現在のアイコン表示（プレビュー用ID: current_avatar） --}}
                            <div class="mb-2">
                                <img id="current_avatar"
                                    {{-- 既存の画像があれば表示。なければユーザー名の頭文字を表示するプレースホルダーを使用 --}}
                                    src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : 'https://placehold.co/100x100/007bff/ffffff?text=' . substr(Auth::user()->name, 0, 1) }}" 
                                    alt="" 
                                    class="rounded-circle" 
                                    style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #ccc;">
                            </div>

                            {{-- ファイル選択インプットと削除ボタンのコンテナ --}}
                            <div class="d-flex flex-column align-items-center w-75 mx-auto">
                                <div class="custom-file w-100 mb-2">
                                    <input type="file" 
                                        class="custom-file-input @error('image') is-invalid @enderror" 
                                        id="image" 
                                        name="image" 
                                        accept="image/*"
                                        onchange="document.getElementById('file_name_display').textContent = this.files[0].name; previewImage(this); resetDeleteFlag();">
                                    <label class="custom-file-label" for="image" data-browse="選択">
                                        <span id="file_name_display">ファイルを選択</span>
                                    </label>
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                {{-- 【新規追加】画像削除ボタン (現在画像がある場合のみ表示) --}}
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger w-40" 
                                        id="delete_image_btn" 
                                        onclick="deleteImage();">
                                    <i class="fas fa-trash mr-1"></i>画像を削除
                                </button>
                            </div>
                        </div>

                        {{-- 1. ユーザー名 (name) --}}
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">ユーザー名 <span class="badge badge-danger">必須</span></label>
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', Auth::user()->name) }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="例：山田 太郎"
                            >
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 2. メールアドレス (email) --}}
                        <div class="form-group">
                            <label for="email" class="font-weight-bold">メールアドレス <span class="badge badge-danger">必須</span></label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email', Auth::user()->email) }}"
                                required
                                autocomplete="username"
                                placeholder="example@example.com"
                            >
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group row mb-0 mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg font-weight-bold shadow px-5">
                                    <i class="fas fa-save mr-2"></i> プロフィールを保存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===================================
               2. パスワード更新セクション
            =================================== --}}
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-warning text-white h4">
                    <i class="fas fa-lock mr-2"></i>パスワードの更新
                </div>
                <div class="card-body p-4">
                    
                    {{-- フォームアクション: UserController@updatePassword に向ける --}}
                    <form method="POST" action="{{ route('user-password.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- 1. 現在のパスワード --}}
                        <div class="form-group">
                            <label for="current_password" class="font-weight-bold">現在のパスワード <span class="badge badge-danger">必須</span></label>
                            <input id="current_password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                name="current_password"
                                required
                                autocomplete="current-password"
                            >
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 2. 新しいパスワード --}}
                        <div class="form-group">
                            <label for="password" class="font-weight-bold">新しいパスワード <span class="badge badge-danger">必須</span></label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                required
                                autocomplete="new-password"
                            >
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 3. パスワードの確認 --}}
                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold">新しいパスワード（確認） <span class="badge badge-danger">必須</span></label>
                            <input id="password_confirmation" type="password"
                                class="form-control"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            >
                        </div>

                        <div class="form-group row mb-0 mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-warning btn-lg text-white font-weight-bold shadow px-5">
                                    <i class="fas fa-key mr-2"></i> パスワードを変更
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- アイコン画像のプレビューと削除フラグ設定用 JavaScript --}}
<script>
    // デフォルトのプレースホルダー画像のURLを生成
    const defaultPlaceholderUrl = `https://placehold.co/100x100/007bff/ffffff?text={{ substr(Auth::user()->name, 0, 1) }}`;

    /**
     * 選択された画像のプレビューを表示する
     * @param {HTMLInputElement} input - ファイル入力要素
     */
    function previewImage(input) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current_avatar').src = e.target.result;
        }
        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * 削除フラグをリセットする (新しいファイルが選択された場合)
     */
    function resetDeleteFlag() {
        document.getElementById('delete_image_input').value = '0';
    }

    /**
     * 画像削除フラグをセットし、プレースホルダーを表示する
     */
    function deleteImage() {
        // 1. 隠しフィールドの値を '1' にセット
        document.getElementById('delete_image_input').value = '1';
        
        // 2. プレビューをデフォルトのプレースホルダーに戻す
        document.getElementById('current_avatar').src = defaultPlaceholderUrl;
        
        // 3. ファイル選択フィールドの内容をクリア（ブラウザによって挙動が異なる場合があるため）
        const fileInput = document.getElementById('image');
        fileInput.value = ''; // ファイル名をクリア
        document.getElementById('file_name_display').textContent = 'ファイルを選択'; // 表示をリセット
    }
</script>
@endsection