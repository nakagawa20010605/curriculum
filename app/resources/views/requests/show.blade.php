@extends('layouts.app')

@section('title', '依頼詳細: ' . $serviceRequest->service->title)

@section('content')
<div class="container py-4">
    
    {{-- ★ エラーメッセージ表示エリア ★ --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <h1 class="mb-4 font-weight-bold">
        <i class="fas fa-clipboard-list mr-2 text-primary"></i>
        依頼詳細: {{ $serviceRequest->service->title }}
    </h1>

    <div class="row">
        
        {{-- ===================================
           1. 依頼の詳細情報 (左側/上側)
        =================================== --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0 font-weight-bold">依頼概要</span>
                    {{-- ステータスバッジ --}}
                    @php
                        // ステータスに応じたバッジの色を設定
                        $statusBadgeClass = [
                            'pending' => 'badge-warning text-dark',
                            'accepted' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'completed' => 'badge-primary',
                            'cancelled' => 'badge-secondary',
                        ][$serviceRequest->status] ?? 'badge-info';
                    @endphp
                    <span class="badge {{ $statusBadgeClass }} py-2 px-3 text-uppercase font-weight-bold">
                        {{ $serviceRequest->status }}
                    </span>
                </div>
                
                <div class="card-body">
                    
                    {{-- サービスへのリンク --}}
                    <div class="alert alert-info py-2 mb-4">
                        <i class="fas fa-link mr-1"></i> 
                        サービス: 
                        <a href="{{ route('services.show', $serviceRequest->service) }}" class="alert-link font-weight-bold">
                            {{ $serviceRequest->service->title }} (¥{{ number_format($serviceRequest->service->amount) }})
                        </a>
                    </div>
                    
                    <h6 class="font-weight-bold text-muted border-bottom pb-1 mb-2">依頼者からのメッセージ</h6>
                    <p class="card-text border p-3 bg-light rounded">{!! nl2br(e($serviceRequest->description)) !!}</p>
                    
                    <hr>

                    {{-- 連絡先詳細 --}}
                    <h6 class="font-weight-bold text-muted border-bottom pb-1 mb-3 mt-4">連絡先・希望納期</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-secondary">メールアドレス</label>
                            <p class="p-2 border rounded bg-white">{{ $serviceRequest->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-secondary">電話番号</label>
                            <p class="p-2 border rounded bg-white">{{ $serviceRequest->tel }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-secondary">希望納期</label>
                            <p class="p-2 border rounded bg-white">
                                @if ($serviceRequest->deadline)
                                    <i class="far fa-calendar-alt mr-1"></i> 
                                    {{ \Carbon\Carbon::parse($serviceRequest->deadline)->format('Y年m月d日') }}
                                @else
                                    <span class="text-muted">（指定なし）</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-secondary">依頼日</label>
                            <p class="p-2 border rounded bg-white">
                                {{ $serviceRequest->created_at->format('Y/m/d H:i') }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===================================
           2. 相手の情報とアクション (右側/下側)
        =================================== --}}
        <div class="col-lg-4">
            
            {{-- 相手の情報カード --}}
            <div class="card shadow-lg mb-4">
                @php
                    // 依頼者か提供者かによって表示する相手を切り替える
                    $isProvider = Auth::id() === $serviceRequest->service->user_id;
                    $otherUser = $isProvider ? $serviceRequest->requester : $serviceRequest->service->user;
                    $roleText = $isProvider ? '依頼者' : 'サービス提供者';
                @endphp
                
                <div class="card-header bg-info text-white h5">
                    <i class="fas fa-user-tag mr-2"></i>{{ $roleText }}情報
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                    <h4 class="mb-1">{{ optional($otherUser)->name ?? '（ユーザー削除済）' }}</h4>
                    <p class="text-secondary small mb-3">
                        @if ($isProvider)
                            この人にサービスを提供します
                        @else
                            この人がサービスを提供してくれます
                        @endif
                    </p>
                    <a href="{{ route('users.show', $otherUser) }}" class="btn btn-outline-info btn-sm">プロフィールを見る</a>
                </div>
            </div>

            {{-- 依頼ステータス管理フォーム（提供者側のみ） --}}
            @if ($isProvider)
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white h5">
                        <i class="fas fa-sync-alt mr-2"></i>ステータス変更
                    </div>
                    <div class="card-body">
                        {{-- 既に完了/キャンセルされている場合はフォームを表示しない --}}
                        @if ($serviceRequest->status === 'completed' || $serviceRequest->status === 'cancelled')
                            <div class="alert alert-secondary text-center">
                                この依頼は既に**{{ $serviceRequest->status === 'completed' ? '完了' : 'キャンセル' }}**しています。
                            </div>
                        @else
                            <form action="{{ route('requests.update', $serviceRequest) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="status" class="font-weight-bold">新しいステータス</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="pending" @if ($serviceRequest->status === 'pending') selected @endif>保留中 (pending)</option>
                                        <option value="accepted" @if ($serviceRequest->status === 'accepted') selected @endif>承認 (accepted)</option>
                                        <option value="rejected" @if ($serviceRequest->status === 'rejected') selected @endif>拒否 (rejected)</option>
                                        <option value="completed" @if ($serviceRequest->status === 'completed') selected @endif>完了 (completed)</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    ステータスを更新する
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 依頼キャンセルボタン（依頼者側のみ） --}}
            @if (!$isProvider && $serviceRequest->status !== 'completed' && $serviceRequest->status !== 'cancelled')
                <div class="card shadow-lg">
                    <div class="card-body">
                        <form action="{{ route('requests.destroy', $serviceRequest) }}" method="POST" onsubmit="return confirm('本当にこの依頼をキャンセルしますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-times-circle mr-1"></i> 依頼をキャンセルする
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection