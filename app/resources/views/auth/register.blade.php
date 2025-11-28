@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white h4 text-center">
                    <i class="fas fa-user-plus mr-2"></i>新規登録
                </div>

                <div class="card-body p-4">
                    {{-- ★ポイント1: enctype="multipart/form-data" を追加★ --}}
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. ユーザー名 --}}
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right font-weight-bold">ユーザー名</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- 2. メールアドレス --}}
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right font-weight-bold">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- ★画像プレビュー要素とJSロジックを追加★ --}}
                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right font-weight-bold">ユーザーアイコン (任意)</label>

                            <div class="col-md-6">
                                {{-- 画像プレビューエリア --}}
                                <div class="mb-3 d-flex flex-column align-items-center">
                                    {{-- デフォルトアイコンを表示。選択されたらJSで更新されます --}}
                                    <img id="image-preview" 
                                         src="{{ asset('images/default_icon.png') }}" 
                                         alt="" 
                                         class="img-thumbnail rounded-circle mb-2" 
                                         style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #007bff;"
                                    >
                                    {{-- 画像クリアボタン。初期状態では非表示 --}}
                                    <button type="button" id="clear-image-btn" class="btn btn-sm btn-danger shadow-sm" style="display: none;">
                                        <i class="fas fa-times-circle mr-1"></i>クリア
                                    </button>
                                </div>
                                
                                <div class="custom-file">
                                    <input id="image" type="file" 
                                        class="custom-file-input @error('image') is-invalid @enderror" 
                                        name="image" 
                                        accept="image/*"
                                    >
                                    <label class="custom-file-label" for="image" data-browse="選択">ファイルを選択...</label>
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">プロフィール用の画像をアップロードしてください (最大2MB)。</small>
                            </div>
                        </div>
                        {{-- 3. パスワード --}}
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right font-weight-bold">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- 4. パスワード確認 --}}
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right font-weight-bold">パスワード確認</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        
                        {{-- ファイル名表示と画像プレビューのためのJS --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const imageInput = document.getElementById('image');
                                const imagePreview = document.getElementById('image-preview');
                                const customFileLabel = imageInput.nextElementSibling;
                                const clearButton = document.getElementById('clear-image-btn');
                                
                                // 初期状態のアイコンURLを保存 (リセット時に使用)
                                const initialIconUrl = imagePreview ? imagePreview.src : '';

                                // 1. 画像ファイルが選択された時の処理
                                imageInput.addEventListener('change', function (e) {
                                    const file = e.target.files[0];
                                    
                                    if (file) {
                                        // FileReaderでファイルを読み込み、プレビューを表示
                                        const reader = new FileReader();
                                        
                                        reader.onload = function(e) {
                                            if (imagePreview) {
                                                imagePreview.src = e.target.result;
                                            }
                                        };
                                        
                                        reader.readAsDataURL(file);

                                        // ファイル名を表示
                                        customFileLabel.innerText = file.name;

                                        // クリアボタンを表示
                                        if (clearButton) {
                                            clearButton.style.display = 'inline-block';
                                        }

                                    } else {
                                        // ファイルが選択されていない（クリアされた）場合
                                        resetImage();
                                    }
                                });

                                // 2. クリアボタンが押された時の処理
                                if (clearButton) {
                                    clearButton.addEventListener('click', function() {
                                        resetImage();
                                    });
                                }
                                
                                // 3. 画像とフィールドをリセットする関数
                                function resetImage() {
                                    // input type="file" の値をリセット (これで送信データからファイルが除外される)
                                    imageInput.value = '';
                                    
                                    // プレビューを初期状態（デフォルトアイコン）に戻す
                                    if (imagePreview) {
                                        imagePreview.src = initialIconUrl;
                                    }

                                    // ファイル名表示をリセット
                                    customFileLabel.innerText = 'ファイルを選択...';

                                    // クリアボタンを非表示
                                    if (clearButton) {
                                        clearButton.style.display = 'none';
                                    }
                                }
                            });
                        </script>

                        {{-- 5. 送信ボタン --}}
                        <div class="form-group row mb-0 mt-4">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow-sm w-100">
                                    <i class="fas fa-sign-in-alt mr-2"></i>登録
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection