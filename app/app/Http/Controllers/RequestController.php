<?php

namespace App\Http\Controllers;

use App\Service;
use App\ServiceRequest;
use App\ServiceRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Exception;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 新しいリクエスト（依頼）を保存
     */
    public function store(Request $request, Service $service)
    {
        // バリデーション
        $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'tel' => ['nullable', 'string', 'digits_between:10,11', 'regex:/^0\d{9,10}$/'],
            'email' => ['required', 'email', 'max:255'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $user = Auth::user();

        // 自分のサービスへの依頼は禁止
        if ($user->id === $service->user_id) {
            return back()->with('error', 'ご自身のサービスには依頼できません。');
        }

        DB::beginTransaction();
        
        \Log::info('store reached', $request->all());
        try {
            // 保存処理
            \Log::info('before create', [
                'data' => [
                    'service_id' => $service->id,
                    'user_id' => $user->id,
                    'description' => $request->description,
                    'tel' => $request->tel,
                    'email' => $request->email,
                    'deadline' => $request->deadline,
                    'status' => ServiceRequestStatus::PENDING,
                    ]
                ]);

            ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'description' => $request->description,
                'tel' => $request->tel,
                'email' => $request->email,
                'deadline' => $request->deadline,
                'status' => ServiceRequestStatus::PENDING, // 初期：0
            ]);
            \Log::info('create OK');

            DB::commit();

            return redirect()->route('home')
                ->with('success', '依頼を送信しました。');
        } catch (Exception $e) {
            DB::rollBack();

            \Log::error('Request store failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', '依頼の送信中にエラーが発生しました。');
        }
    }

    /**
     * 依頼一覧
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 自分が依頼したもの
        $requestsMade = $user->requestsMade()->with('service.user')->latest()->get();

        // 自分のサービスに届いた依頼
        $requestsReceived = $user->requestsReceived()->with('requester', 'service')->latest()->get();

        return view('requests.index', compact('requestsMade', 'requestsReceived'));
    }

    /**
     * 依頼の詳細
     */
    public function show(ServiceRequest $serviceRequest)
    {
    $providerId = $serviceRequest->service->user_id;
    $requesterId = $serviceRequest->user_id;
    $currentUserId = Auth::id();

    // 閲覧権限チェック
    if ($currentUserId !== $providerId && $currentUserId !== $requesterId) {
        return redirect()->route('requests.index')->with('error', '閲覧権限がありません。');
    }

    return view('requests.show', compact('serviceRequest'));
    }

    /**
     * ステータス更新
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->service->user_id) {
            return back()->with('error', '権限がありません。');
        }

        if (in_array($serviceRequest->status, [
            ServiceRequestStatus::COMPLETED,
            ServiceRequestStatus::DELETED
        ])) {
            return back()->with('error', 'この依頼は更新できません。');
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'integer',
                Rule::in([
                    ServiceRequestStatus::IN_PROGRESS,
                    ServiceRequestStatus::COMPLETED,
                    ServiceRequestStatus::DELETED,
                ]),
            ],
        ]);

        $serviceRequest->status = $validated['status'];
        $serviceRequest->save();

        return redirect()->route('requests.show', $serviceRequest)
            ->with('status', 'ステータスを更新しました。');
    }

    /**
     * キャンセル（依頼者側）
     */
    public function destroy(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id) {
            return back()->with('error', '権限がありません。');
        }

        if (in_array($serviceRequest->status, [
            ServiceRequestStatus::COMPLETED,
            ServiceRequestStatus::DELETED
        ])) {
            return back()->with('error', '既に完了または削除済みです。');
        }

        $serviceRequest->status = ServiceRequestStatus::DELETED;
        $serviceRequest->save();

        return redirect()->route('requests.index');
    }
}