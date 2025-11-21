@extends('layouts.app')

@section('title', 'ホーム画面')

@section('content')
<div class="container">
    <h1 class="mb-4">投稿一覧</h1>

    {{-- ★ 投稿検索エリア (後で実装) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h2 class="card-title h4">投稿を検索する</h2>
            {{-- 検索フォームは後で追加 --}}
            <p class="card-text text-muted">（このエリアに検索フォームが追加されます）</p>
        </div>
    </div>

    {{-- ★ 投稿カードリスト ★ --}}
    @if ($services->isEmpty())
        <p class="text-center text-muted h5">現在、投稿はありません。</p>
    @else
        <div class="row">
            {{-- データベースから取得した投稿をループで表示 --}}
            @foreach ($services as $service)
                {{-- カードの列幅を調整 (デスクトップ:3列, タブレット:4列, スマホ:6列) --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        
                        {{-- 投稿画像 --}}
                        @if ($service->image)
                            {{-- storage/services ディレクトリ内の画像を指す --}}
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                        @else
                            {{-- 画像がない場合のプレースホルダー --}}
                            <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-top" style="height: 180px;">
                                <i class="fas fa-camera fa-2x"></i> No Image
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            {{-- タイトル --}}
                            <h5 class="card-title text-truncate mb-2">
                                {{ $service->title }}
                            </h5>
                            
                            {{-- 金額 --}}
                            <p class="h4 font-weight-bold text-primary mb-3">
                                ¥{{ number_format($service->amount) }}
                            </p>

                            {{-- ユーザー名（投稿者） --}}
                            <p class="card-text text-muted small mt-auto">
                                <i class="fas fa-user-circle mr-1"></i> 投稿者: {{ $service->user->name }}
                            </p>
                            
                            {{-- 詳細リンクボタン --}}
                            <a href="{{ route('services.show', $service) }}" class="btn btn-primary btn-block mt-3">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ★ ページネーションリンク ★ --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection