@extends('layouts.app')

@section('title', '違反報告フォーム')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                
                {{-- カードヘッダー: 違反報告フォームのタイトル --}}
                <div class="card-header bg-danger text-white">
                    {{-- Font Awesome v5のクラスを想定 (mr-2はme-2に修正) --}}
                    <h1 class="h4 mb-0"><i class="fas fa-flag me-2"></i> 違反報告フォーム</h1>
                </div>

                <div class="card-body p-4">

                    {{-- 報告対象サービスの情報 --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h5 class="font-weight-bold text-danger mb-2">報告対象サービス</h5>
                        <p class="mb-1"><strong>タイトル:</strong> {{ $service->title }}</p>
                        {{-- ユーザーリレーションシップがあることを想定 --}}
                        <p class="mb-1"><strong>投稿者:</strong> {{ $service->user->name }}</p>
                        <p class="mb-0 text-muted small">
                            報告内容が不正確だった場合、調査結果に基づいてアカウントに制限がかかる可能性があります。
                        </p>
                    </div>

                    {{-- フォーム本体: ReportControllerのstoreメソッドにデータを送信 --}}
                    <form action="{{ route('reports.store', $service) }}" method="POST">
                        @csrf

                        {{-- 詳細な説明の入力フィールド (名前は 'details' を使用) --}}
                        <div class="form-group mb-4">
                            <label for="details" class="font-weight-bold text-secondary">
                                {{-- Bootstrap 5 のバッジクラスに修正 --}}
                                <span class="badge bg-danger me-1">必須</span> 詳細な説明 (最大255文字)
                            </label>
                            <textarea class="form-control @error('details') is-invalid @enderror shadow-sm" 
                                id="details" 
                                name="details" {{-- コントローラーでの入力名に合わせて details を使用 --}}
                                rows="6" 
                                maxlength="255" 
                                placeholder="どのような違反行為が行われているか、具体的な状況を詳しく記入してください。" 
                                required>{{ old('details') }}</textarea>
                            
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- テキスト右寄せを text-end に修正 --}}
                            <small class="form-text text-muted text-end">
                                最大 255 文字
                            </small>
                        </div>

                        {{-- 送信・戻るボタン --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('services.show', $service) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-1"></i> 戻る
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg shadow-lg">
                                <i class="fas fa-paper-plane me-1"></i> 報告を送信する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection