@extends('layouts.app')

@section('title', $user->name . 'のプロフィール')

@section('content')
<div class="container py-4">
    {{-- ★ 成功時のフラッシュメッセージ表示 ★ --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow mb-4" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- ===================================
           1. プロフィール情報セクション (左側/上側)
        =================================== --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-info text-white h5">
                    <i class="fas fa-id-card-alt mr-2"></i>プロフィール
                </div>
                <div class="card-body text-center">
                    {{-- 【修正箇所】アバター画像表示ロジック --}}
                    {{-- データベースの image パスが存在すればそれを使用し、なければプレースホルダーを使用 --}}
                    <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://placehold.co/150x150/007bff/ffffff?text=' . substr($user->name, 0, 1) }}" 
                         alt="User Avatar" 
                         class="rounded-circle mb-3" 
                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #007bff;">

                    <h4 class="card-title font-weight-bold">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    <hr>
                    
                    {{-- ユーザーがログインユーザー自身の場合のみ、編集ボタンを表示 --}}
                    @if (Auth::check() && Auth::id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block shadow">
                            <i class="fas fa-cog mr-2"></i>プロフィールを編集
                        </a>
                    @endif

                    {{-- 統計情報（オプション） --}}
                    <div class="mt-4">
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <h6 class="font-weight-bold mb-0">{{ $user->services->count() }}</h6>
                                <small class="text-secondary">投稿数</small>
                            </div>
                            <div class="text-center">
                                {{-- 実際にはリレーションが必要です --}}
                                <h6 class="font-weight-bold mb-0">XX</h6> 
                                <small class="text-secondary">リクエスト</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================================
           2. ユーザーの投稿一覧セクション (右側/下側)
        =================================== --}}
        <div class="col-md-8">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-info text-white h5">
                    <i class="fas fa-list-alt mr-2"></i>{{ $user->name }} の投稿一覧
                </div>
                <div class="card-body p-0">
                    
                    @if ($services->isEmpty())
                        <div class="p-4 text-center text-muted">
                            まだ投稿がありません。
                            @if (Auth::check() && Auth::id() === $user->id)
                                <div class="mt-2">
                                    <a href="{{ route('services.create') }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-plus-circle mr-1"></i> 新しい投稿を作成
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($services as $service)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        {{-- 投稿画像 (サムネイル) --}}
                                        <img src="{{ $service->image ? asset('storage/' . $service->image) : 'https://placehold.co/50x50/3490dc/ffffff?text=NoImg' }}" 
                                             alt="{{ $service->title }}" 
                                             style="width: 50px; height: 50px; object-fit: cover;" 
                                             class="rounded mr-3">

                                        <div>
                                            {{-- 投稿タイトル --}}
                                            <a href="{{ route('services.show', $service) }}" class="h6 mb-0 text-dark font-weight-bold">
                                                {{ $service->title }}
                                            </a>
                                            <p class="mb-0 text-success small font-weight-bold">¥{{ number_format($service->amount) }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- アクションボタン --}}
                                    <div>
                                        {{-- 詳細ボタン --}}
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-info mr-2">
                                            <i class="fas fa-eye"></i> 詳細
                                        </a>

                                        {{-- ログインユーザーの投稿であれば、編集ボタンを表示 --}}
                                        @if (Auth::check() && Auth::id() === $service->user_id)
                                            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-pencil-alt"></i> 編集
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                </div>
                {{-- ページネーション (Controller側で対応する場合) --}}
                @if ($services instanceof \Illuminate\Pagination\AbstractPaginator && $services->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $services->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection