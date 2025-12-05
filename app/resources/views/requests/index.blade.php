@extends('layouts.app')

@section('title', '依頼・リクエスト一覧')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 font-weight-bold text-center">
        <i class="fas fa-handshake mr-2 text-primary"></i>依頼・リクエスト管理
    </h1>
    
    {{-- 依頼のステータス更新後のフラッシュメッセージを表示 --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    {{-- タブナビゲーション --}}
    <ul class="nav nav-tabs nav-justified mb-4" id="requestTabs" role="tablist">
        {{-- 自分が依頼したタブ --}}
        <li class="nav-item">
            <a class="nav-link active h5" id="made-tab" data-toggle="tab" href="#made" role="tab" aria-controls="made" aria-selected="true">
                <i class="fas fa-paper-plane mr-2"></i>自分が**依頼した**もの ({{ $requestsMade->count() }})
            </a>
        </li>
        {{-- 自分が受けたリクエストタブ --}}
        <li class="nav-item">
            <a class="nav-link h5" id="received-tab" data-toggle="tab" href="#received" role="tab" aria-controls="received" aria-selected="false">
                <i class="fas fa-inbox mr-2"></i>**受けた**依頼 ({{ $requestsReceived->count() }})
            </a>
        </li>
    </ul>

    {{-- タブコンテンツ --}}
    <div class="tab-content" id="requestTabsContent">
        
        {{-- ===============================================
           1. 自分が依頼者として作成したリクエスト (requestsMade)
        =============================================== --}}
        <div class="tab-pane fade show active" id="made" role="tabpanel" aria-labelledby="made-tab">
            @if ($requestsMade->isEmpty())
                <div class="alert alert-secondary text-center">
                    まだ、依頼を作成していません。
                    <a href="{{ route('home') }}" class="alert-link">ホーム画面からサービスを探してみましょう。</a>
                </div>
            @else
                <div class="list-group shadow-sm">
                    @foreach ($requestsMade as $request)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap py-3">
                            <div class="flex-grow-1">
                                <p class="h5 mb-1 font-weight-bold">
                                    <span class="badge badge-secondary mr-2">依頼</span>
                                    <a href="{{ route('services.show', $request->service) }}" class="text-decoration-none text-dark">
                                        {{ $request->service->title }}
                                    </a>
                                </p>
                                <p class="mb-1 text-muted small">
                                    提供者: {{ optional($request->service->user)->name ?? '（ユーザー削除済）' }} |
                                    依頼日: {{ $request->created_at->format('Y/m/d') }}
                                </p>
                                <p class="mb-0 mt-2 text-dark font-weight-normal text-truncate" style="max-width: 90%;">
                                    **依頼内容:** {{ $request->description }}
                                </p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                {{-- ステータスバッジ --}}
                                <span class="badge 
                                    @if ($request->status === 'pending') badge-warning text-dark
                                    @elseif ($request->status === 'accepted') badge-success
                                    @elseif ($request->status === 'rejected') badge-danger
                                    @elseif ($request->status === 'completed') badge-primary
                                    @elseif ($request->status === 'cancelled') badge-secondary
                                    @else badge-info 
                                    @endif 
                                    py-2 px-3 mr-3 text-uppercase font-weight-bold">
                                    {{ $request->status }}
                                </span>
                                
                                {{-- 詳細ボタン --}}
                                <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline-info">
                                    詳細
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===============================================
           2. 自分のサービスに対するリクエスト (requestsReceived)
        =============================================== --}}
        <div class="tab-pane fade" id="received" role="tabpanel" aria-labelledby="received-tab">
             @if ($requestsReceived->isEmpty())
                <div class="alert alert-secondary text-center">
                    まだ、あなたのサービスに対する依頼は届いていません。
                </div>
            @else
                <div class="list-group shadow-sm">
                    @foreach ($requestsReceived as $request)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap py-3">
                             <div class="flex-grow-1">
                                <p class="h5 mb-1 font-weight-bold">
                                    <span class="badge badge-primary mr-2">リクエスト</span>
                                    <a href="{{ route('services.show', $request->service) }}" class="text-decoration-none text-dark">
                                        {{ $request->service->title }}
                                    </a>
                                </p>
                                <p class="mb-1 text-muted small">
                                    依頼者: {{ optional($request->requester)->name ?? '（ユーザー削除済）' }} |
                                    依頼日: {{ $request->created_at->format('Y/m/d') }}
                                </p>
                                <p class="mb-0 mt-2 text-dark font-weight-normal text-truncate" style="max-width: 90%;">
                                    **依頼内容:** {{ $request->description }}
                                </p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                {{-- ステータスバッジ --}}
                                <span class="badge 
                                    @if ($request->status === 'pending') badge-warning text-dark
                                    @elseif ($request->status === 'accepted') badge-success
                                    @elseif ($request->status === 'rejected') badge-danger
                                    @elseif ($request->status === 'completed') badge-primary
                                    @elseif ($request->status === 'cancelled') badge-secondary
                                    @else badge-info 
                                    @endif 
                                    py-2 px-3 mr-3 text-uppercase font-weight-bold">
                                    {{ $request->status }}
                                </span>

                                {{-- 詳細ボタン --}}
                                <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-info">
                                    対応
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection