@extends('layouts.app')

@section('title', '投稿編集')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white h4">
                    <i class="fas fa-edit mr-2"></i>投稿を編集
                </div>

                <div class="card-body p-4">
                    {{-- フォームアクション: ServiceController@update に向ける。PUT/PATCHメソッドを使用。 --}}
                    <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH') {{-- 更新処理には PATCH メソッドを使用 --}}

                        {{-- 1. タイトル --}}
                        <div class="form-group">
                            <label for="title" class="font-weight-bold">タイトル <span class="badge badge-danger">必須</span></label>
                            <input id="title" type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                name="title" 
                                value="{{ old('title', $service->title) }}" {{-- 既存の値をセット --}}
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
                                    value="{{ old('amount', $service->amount) }}" {{-- 既存の値をセット --}}
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
                            >{{ old('description', $service->description) }}</textarea> {{-- 既存の値をセット --}}
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 4. サービス画像 (画像プレビュー機能を追加) --}}
                        <div class="form-group">
                            <label for="image" class="font-weight-bold">サービス画像 (任意)</label>

                            {{-- ★画像プレビューエリア★ --}}
                            <div class="mb-3 d-flex flex-column align-items-center border border-light rounded p-2">
                                @php
                                    // 既存の画像URL、またはプレースホルダーURLを決定
                                    $imageUrl = $service->image 
                                        ? asset('storage/' . $service->image) 
                                        : 'https://placehold.co/300x200/dddddd/333333?text=サービス画像プレビュー';
                                @endphp
                                
                                <img id="image-preview" 
                                    src="{{ $imageUrl }}" 
                                    alt="画像プレビュー" 
                                    class="img-thumbnail mb-2" 
                                    style="max-width: 100%; height: auto; border: 2px solid #007bff;"
                                >
                                
                                {{-- 画像クリアボタン。既存画像がある場合のみ表示（新規画像を選択した場合のリセットも兼ねる） --}}
                                <button type="button" id="clear-image-btn" 
                                    class="btn btn-sm btn-danger shadow-sm" 
                                    style="display: {{ $service->image ? 'inline-block' : 'none' }};"
                                >
                                    <i class="fas fa-times-circle mr-1"></i>画像リセット
                                </button>
                            </div>

                            {{-- 既存画像がある場合の「削除チェックボックス」と「現在の画像」ラベルの調整 --}}
                            @if ($service->image)
                                <div class="form-check mb-3" id="delete-image-check">
                                    {{-- 画像削除チェックボックス: オンにした場合、既存画像が削除される --}}
                                    {{-- JSでこのチェックボックスの状態をリセット処理と連動させます --}}
                                    <input class="form-check-input" type="checkbox" name="delete_image" id="delete_image" value="1">
                                    <label class="form-check-label text-danger" for="delete_image">
                                        元の画像を削除して保存する
                                    </label>
                                </div>
                            @endif
                            
                            <label for="image" class="font-weight-bold mt-3">新しいサービス画像を選択</label>
                            <div class="custom-file">
                                <input id="image" type="file" 
                                    class="custom-file-input @error('image') is-invalid @enderror" 
                                    name="image"
                                    accept="image/*"
                                >
                                <label class="custom-file-label" for="image" data-browse="選択">新しいファイルを選択...</label>
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">新しい画像をアップロードすると、現在の画像は上書きされます。</small>
                        </div>
                        
                        {{-- ★ファイル名表示、画像プレビュー、クリア処理のためのJSに更新★ --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const imageInput = document.getElementById('image');
                                const imagePreview = document.getElementById('image-preview');
                                const customFileLabel = imageInput ? imageInput.nextElementSibling : null;
                                const clearButton = document.getElementById('clear-image-btn');
                                const deleteCheckbox = document.getElementById('delete_image');
                                const deleteCheckContainer = document.getElementById('delete-image-check');

                                // 既存の画像URLとプレースホルダーURLを保存
                                const initialServiceUrl = "{{ $service->image ? asset('storage/' . $service->image) : '' }}";
                                const placeholderUrl = "https://placehold.co/300x200/dddddd/333333?text=サービス画像プレビュー";

                                // 1. 画像ファイルが選択された時の処理
                                if (imageInput) {
                                    imageInput.addEventListener('change', function (e) {
                                        const file = e.target.files[0];
                                        
                                        if (file) {
                                            // ファイルを読み込み、プレビューを表示
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

                                            // 新しいファイルを選択した場合は、既存画像削除チェックを解除
                                            if (deleteCheckbox) {
                                                deleteCheckbox.checked = false;
                                                // 削除チェックボックスを無効化（新しい画像が優先されるため）
                                                if (deleteCheckContainer) {
                                                    deleteCheckContainer.style.opacity = '0.5';
                                                }
                                                deleteCheckbox.disabled = true;
                                            }

                                        } else {
                                            // ファイルが選択されていない（クリアされた）場合
                                            resetImage();
                                        }
                                    });
                                }

                                // 2. クリアボタンが押された時の処理 (ファイル選択をリセット)
                                if (clearButton) {
                                    clearButton.addEventListener('click', function() {
                                        resetImage();
                                    });
                                }

                                // 3. 既存画像削除チェックボックスの状態変更時の処理
                                if (deleteCheckbox) {
                                    deleteCheckbox.addEventListener('change', function() {
                                        // 削除にチェックが入った場合、プレビューをプレースホルダーに変更
                                        if (this.checked) {
                                            if (imagePreview) {
                                                imagePreview.src = placeholderUrl;
                                            }
                                            // ファイル選択フィールドもクリア（新しいファイルアップロードと同時に削除はできないため）
                                            if (imageInput) {
                                                imageInput.value = '';
                                            }
                                            if (customFileLabel) {
                                                customFileLabel.innerText = '新しいファイルを選択...';
                                            }
                                        } else {
                                            // チェックが外れた場合、元の画像があれば元に戻す
                                            if (imagePreview && initialServiceUrl) {
                                                imagePreview.src = initialServiceUrl;
                                            }
                                        }
                                    });
                                }

                                // 4. 画像とファイル選択フィールドをリセットする関数
                                function resetImage() {
                                    // input type="file" の値をリセット
                                    if (imageInput) {
                                        imageInput.value = '';
                                    }
                                    
                                    // プレビューを既存の画像、またはプレースホルダーに戻す
                                    if (imagePreview) {
                                        imagePreview.src = initialServiceUrl ? initialServiceUrl : placeholderUrl;
                                    }

                                    // ファイル名表示をリセット
                                    if (customFileLabel) {
                                        customFileLabel.innerText = '新しいファイルを選択...';
                                    }

                                    // クリアボタンの表示状態を初期状態に戻す
                                    if (clearButton) {
                                        clearButton.style.display = initialServiceUrl ? 'inline-block' : 'none';
                                    }
                                    
                                    // 削除チェックボックスを有効化し、状態をリセット
                                    if (deleteCheckbox) {
                                        deleteCheckbox.checked = false;
                                        deleteCheckbox.disabled = false;
                                        if (deleteCheckContainer) {
                                            deleteCheckContainer.style.opacity = '1.0';
                                        }
                                    }
                                }
                            });
                        </script>


                        {{-- 5. 送信ボタン --}}
                        <div class="form-group row mb-0 mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow px-5">
                                    <i class="fas fa-sync-alt mr-2"></i> 変更を保存
                                </button>
                                <a href="{{ route('services.show', $service) }}" class="btn btn-secondary btn-lg ml-3 px-5">
                                    キャンセル
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
@endsection