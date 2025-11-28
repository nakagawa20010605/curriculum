@extends('layouts.app')

@section('title', '新規投稿')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white h4">
                    <i class="fas fa-plus-circle mr-2"></i>新規投稿を作成
                </div>

                <div class="card-body p-4">
                    {{-- フォームアクションは ServiceController@store に向ける --}}
                    {{-- ファイルアップロードのため enctype を指定 --}}
                    <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. タイトル --}}
                        <div class="form-group">
                            <label for="title" class="font-weight-bold">タイトル <span class="badge badge-danger">必須</span></label>
                            <input id="title" type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                name="title" 
                                value="{{ old('title') }}" 
                                required 
                                placeholder="例：Webサイト制作（5,000円から）"
                            >
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 2. 金額 --}}
                        <div class="form-group">
                            <label for="amount" class="font-weight-bold">金額 (¥) <span class="badge badge-danger">必須</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">¥</span>
                                </div>
                                <input id="amount" type="number" 
                                    class="form-control @error('amount') is-invalid @enderror" 
                                    name="amount" 
                                    value="{{ old('amount') }}" 
                                    required 
                                    min="1" 
                                    placeholder="例：5000"
                                >
                            </div>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 3. 詳細説明 --}}
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">詳細説明 <span class="badge badge-danger">必須</span></label>
                            <textarea id="description" 
                                class="form-control @error('description') is-invalid @enderror" 
                                name="description" 
                                rows="8" 
                                required 
                                placeholder="提供するスキルやサービスの内容、期間、対象者、注意事項などを詳しく記述してください。"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 4. サービス画像 (プレビュー機能を追加) --}}
                        <div class="form-group">
                            <label for="image" class="font-weight-bold">サービス画像 (任意)</label>
                            
                            {{-- ★画像プレビューエリア★ --}}
                            <div class="mb-3 d-flex flex-column align-items-center border border-light rounded p-2">
                                {{-- プレビュー画像。デフォルトはプレースホルダー --}}
                                <img id="image-preview" 
                                    src="https://placehold.co/300x200/dddddd/333333?text=サービス画像プレビュー" 
                                    alt="画像プレビュー" 
                                    class="img-thumbnail mb-2" 
                                    style="max-width: 100%; height: auto; border: 2px solid #28a745;"
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
                            <small class="form-text text-muted">サービスの魅力を伝える画像をアップロードしてください。</small>
                        </div>
                        
                        {{-- ★ファイル名表示と画像プレビューのためのJSに更新★ --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const imageInput = document.getElementById('image');
                                const imagePreview = document.getElementById('image-preview');
                                // ファイル名表示のlabel要素を取得 (nextElementSiblingはcustom-file-inputの次のlabel)
                                const customFileLabel = imageInput ? imageInput.nextElementSibling : null;
                                const clearButton = document.getElementById('clear-image-btn');
                                
                                // 初期状態のプレースホルダーURLを保存 (リセット時に使用)
                                const initialPlaceholderUrl = imagePreview ? imagePreview.src : '';

                                // 1. 画像ファイルが選択された時の処理
                                if (imageInput) {
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
                                            if (customFileLabel) {
                                                customFileLabel.innerText = file.name;
                                            }

                                            // クリアボタンを表示
                                            if (clearButton) {
                                                clearButton.style.display = 'inline-block';
                                            }

                                        } else {
                                            // ファイルが選択されていない（クリアされた）場合
                                            resetImage();
                                        }
                                    });
                                }

                                // 2. クリアボタンが押された時の処理
                                if (clearButton) {
                                    clearButton.addEventListener('click', function() {
                                        resetImage();
                                    });
                                }
                                
                                // 3. 画像とフィールドをリセットする関数
                                function resetImage() {
                                    // input type="file" の値をリセット (これで送信データからファイルが除外される)
                                    if (imageInput) {
                                        imageInput.value = '';
                                    }
                                    
                                    // プレビューを初期状態（プレースホルダー）に戻す
                                    if (imagePreview) {
                                        imagePreview.src = initialPlaceholderUrl;
                                    }

                                    // ファイル名表示をリセット
                                    if (customFileLabel) {
                                        customFileLabel.innerText = 'ファイルを選択...';
                                    }

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
                                <button type="submit" class="btn btn-success btn-lg font-weight-bold shadow">
                                    <i class="fas fa-paper-plane mr-2"></i> 投稿する
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