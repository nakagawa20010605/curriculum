@extends('layouts.app')

@section('title', $service->title)

@section('content')

<div class="container py-4">
<div class="row">

    {{-- 1. サービス詳細情報 (左側/上側) --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow-lg h-100">
            
            {{-- サービス画像 --}}
            @if ($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="card-img-top" style="height: 400px; object-fit: cover;">
            @else
                <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-top" style="height: 400px;">
                    <i class="fas fa-camera fa-4x"></i> No Image
                </div>
            @endif

            <div class="card-body p-4">
                {{-- タイトル --}}
                <h1 class="card-title font-weight-bold mb-3">{{ $service->title }}</h1>
                
                {{-- 金額 --}}
                <div class="d-flex align-items-center mb-4">
                    <span class="badge badge-success h5 py-2 px-3 mr-3">
                        <i class="fas fa-yen-sign mr-1"></i> 価格: ¥{{ number_format($service->amount) }}
                    </span>
                    
                    {{-- 投稿者情報へのリンク --}}
                    <a href="{{ route('users.show', $service->user) }}" class="text-info font-weight-bold">
                        <i class="fas fa-user-circle mr-1"></i> {{ optional($service->user)->name ?? '（ユーザー削除済）' }}
                    </a>
                </div>
                
                {{-- 詳細説明 --}}
                <div class="mb-4">
                    <h2 class="h5 font-weight-bold border-bottom pb-2">サービス詳細</h2>
                    <p class="card-text text-secondary">{!! nl2br(e($service->description)) !!}</p>
                </div>

                {{-- アクションボタン（編集・削除） --}}
                <div class="d-flex justify-content-end">
                    {{-- ログインユーザーの投稿であれば、編集・削除ボタンを表示 --}}
                    @if (Auth::check() && Auth::id() === $service->user_id)
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning text-white mr-2 shadow">
                            <i class="fas fa-pencil-alt"></i> 編集
                        </a>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger shadow">
                                <i class="fas fa-trash-alt"></i> 削除
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-footer text-muted small">
                投稿日: {{ $service->created_at->format('Y/m/d H:i') }}
            </div>
        </div>
    </div>

    {{-- 2. リクエストフォーム (右側/下側) --}}
    <div class="col-lg-4">
        {{-- リクエストフォームの表示条件チェック --}}
        @auth
            @if (Auth::id() === $service->user_id)
                {{-- 投稿者自身の場合 --}}
                <div class="alert alert-info shadow-sm text-center">
                    <i class="fas fa-info-circle mr-1"></i> **ご自身の投稿** のため依頼できません。
                </div>
            @else
                {{-- ログインしており投稿者以外であればフォームを常に表示する --}}
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white h5">
                        <i class="fas fa-hands-helping mr-2"></i>このサービスを依頼する
                    </div>
                    <div class="card-body">
                        {{-- フォームアクションは RequestController@store に向ける --}}
                        <form action="{{ route('requests.store', ['service' => $service->id]) }}" method="POST">
                            @csrf
                            
                            {{-- 1. 依頼の詳細メッセージ (description) --}}
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">依頼の詳細メッセージ <span class="badge badge-danger">必須</span></label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="5" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          required 
                                          placeholder="サービス内容、具体的な要望などを詳しく記入してください。">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            {{-- 2. 電話番号 (tel) --}}
                            <div class="form-group">
                                <label for="tel" class="font-weight-bold">電話番号 <span class="badge badge-danger">必須</span></label>
                                <input id="tel" 
                                          type="tel" 
                                          name="tel" 
                                          class="form-control @error('tel') is-invalid @enderror" 
                                          value="{{ old('tel', Auth::user()->tel ?? '') }}" 
                                          required 
                                          placeholder="09012345678※ハイフン不要">
                                @error('tel')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- 3. メールアドレス (email) --}}
                            <div class="form-group">
                                <label for="email" class="font-weight-bold">メールアドレス <span class="badge badge-danger">必須</span></label>
                                <input id="email" 
                                          type="email" 
                                          name="email" 
                                          class="form-control @error('email') is-invalid @enderror" 
                                          value="{{ old('email', Auth::user()->email) }}"
                                          required 
                                          placeholder="連絡可能なメールアドレス">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- 4. 希望納期 (deadline) --}}
                            <div class="form-group">
                                <label for="deadline" class="font-weight-bold">希望納期 <span class="badge badge-secondary">任意</span></label>
                                <input id="deadline" 
                                          type="date" 
                                          name="deadline" 
                                          class="form-control @error('deadline') is-invalid @enderror" 
                                          value="{{ old('deadline') }}">
                                <small class="form-text text-muted">※ 具体的な納期希望日があれば入力してください。</small>
                                @error('deadline')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-4 shadow-sm">
                                <i class="fas fa-paper-plane mr-2"></i>この内容で依頼する
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @else
            {{-- 未ログインの場合 --}}
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white h5">
                    <i class="fas fa-lock mr-2"></i>依頼するにはログインが必要です
                </div>
                <div class="card-body text-center">
                    <p class="text-secondary">サービスを依頼するには、アカウントにログインするか、新規登録してください。</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block mb-2">ログイン</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-block">新規登録</a>
                </div>
            </div>
        @endauth

        {{-- その他の情報（いいねボタンと報告ボタン） --}}
        <div class="card mt-3 shadow-lg">
            <div class="card-body d-flex justify-content-center align-items-center">
                
                {{-- 修正箇所: ログインしている、かつ投稿者本人でない場合のみいいねボタンを表示する --}}
                @auth
                @if (Auth::id() !== $service->user_id)
                <div class="mr-3">
                @include('components.like-button', [
                    'service' => $service,
                    'isLiked' => $isLiked,
                    'likeCount' => $likeCount
                    ])
                @endif
                </div>
                @endauth
                
                {{-- 報告フォームへのリンク --}}
                <a href="{{ route('reports.create', $service) }}" class="btn btn-outline-danger btn-lg shadow-sm">
                    <i class="fas fa-flag mr-1"></i> 違反報告する
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection