@extends('layouts.app')

@section('title', $service->title . 'の詳細')

@section('content')
<div class="container">
    
    <div class="card shadow-lg mb-5">
        <div class="card-body p-4 p-md-5">

            {{-- ★ 1. タイトルとステータス ★ --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center border-bottom pb-3 mb-4">
                <h1 class="h2 font-weight-bold text-dark mb-2 mb-md-0">
                    {{ $service->title }}
                </h1>
                
                {{-- ステータス表示 --}}
                @php
                    $statusClasses = [
                        0 => ['text' => '掲載中', 'class' => 'badge-success'],
                        1 => ['text' => '進行中', 'class' => 'badge-warning'],
                        2 => ['text' => '完了', 'class' => 'badge-primary'],
                        3 => ['text' => '削除済', 'class' => 'badge-danger'],
                    ];
                    // statusの値に基づいて表示を決定
                    $status = $statusClasses[$service->status] ?? ['text' => '不明', 'class' => 'badge-secondary'];
                @endphp
                <span class="badge badge-pill {{ $status['class'] }} p-2">
                    {{ $status['text'] }}
                </span>
            </div>

            {{-- ★ 2. 画像と主要情報 ★ --}}
            <div class="row mb-4">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    {{-- 投稿画像 --}}
                    @if ($service->image)
                        {{-- asset()でストレージのパスを指定 --}}
                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover;">
                    @else
                        {{-- 画像がない場合のプレースホルダー --}}
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded shadow-sm" style="height: 400px;">
                            <i class="fas fa-camera fa-4x mr-2"></i> No Image
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light p-3 shadow-sm">
                        <div class="list-group list-group-flush">
                            
                            {{-- 金額 --}}
                            <div class="list-group-item bg-light border-0">
                                <p class="text-muted small mb-1">金額</p>
                                <p class="h1 font-weight-bolder text-info">¥{{ number_format($service->amount) }}</p>
                            </div>
                            
                            {{-- 登録日時 --}}
                            <div class="list-group-item bg-light border-0">
                                <p class="text-muted small mb-1">登録日時</p>
                                <p class="mb-0 text-dark">{{ $service->created_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        {{-- 依頼ボタン/編集・削除ボタン --}}
                        @guest
                            {{-- 未ログイン時はログインを促すボタン --}}
                            <a href="{{ route('login') }}" class="btn btn-info btn-block btn-lg font-weight-bold">
                                <i class="fas fa-hands-helping mr-2"></i> 依頼するにはログイン
                            </a>
                        @else
                            {{-- 自分の投稿でなければ依頼ボタンを表示 --}}
                            @if (Auth::id() !== $service->user_id)
                                <a href="#" {{-- TODO: 依頼作成画面へのルートは今後実装 --}}
                                   class="btn btn-primary btn-block btn-lg font-weight-bold shadow-lg">
                                    <i class="fas fa-handshake mr-2"></i> この投稿に依頼する
                                </a>
                            @endif
                        
                            {{-- いいね/通報/編集/削除ボタン --}}
                            <div class="d-flex justify-content-between mt-3">
                                {{-- いいねボタン（仮） --}}
                                <button type="button" class="btn btn-outline-danger flex-fill mr-2">
                                    <i class="far fa-heart mr-1"></i> <span id="like-count">0</span>
                                </button>
                                
                                {{-- 通報ボタン --}}
                                <a href="#" {{-- TODO: 通報画面へのルートは今後実装 --}}
                                   class="btn btn-outline-secondary flex-fill ml-2">
                                    <i class="fas fa-flag mr-1"></i> 通報
                                </a>
                            </div>
                            
                            {{-- 投稿者本人の場合のみ表示される管理操作 --}}
                            @if (Auth::id() === $service->user_id)
                                {{-- 投稿編集ボタン --}}
                                <a href="{{ route('services.edit', $service) }}" class="btn btn-warning btn-block mt-3">
                                    <i class="fas fa-edit mr-1"></i> 投稿を編集
                                </a>
                                {{-- 投稿削除フォーム --}}
                                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block mt-2">
                                        <i class="fas fa-trash-alt mr-1"></i> 投稿を削除
                                    </button>
                                </form>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>

            {{-- ★ 3. 詳細説明 ★ --}}
            <div class="border-top pt-4 mt-4">
                <h2 class="h4 font-weight-bold text-dark mb-3">詳細説明</h2>
                <div class="bg-light p-3 rounded small text-secondary" style="min-height: 100px;">
                    {{-- 改行を認識させるため nl2br を使用し、XSS対策のため e() でエスケープ --}}
                    {!! nl2br(e($service->description)) !!}
                </div>
            </div>
            
            {{-- 4. 戻るボタン --}}
            <div class="mt-5 text-center">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> 投稿一覧へ戻る
                </a>
            </div>
        </div>
    </div>
</div>
@endsection