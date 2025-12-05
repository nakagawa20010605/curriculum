<?php

namespace App\Http\Controllers;

use App\Report; 
use App\Service; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ReportController extends Controller
{
    /**
     * ログイン必須ミドルウェアを設定
     */
    public function __construct()
    {
        // 報告は認証されたユーザーのみが行えるようにする
        $this->middleware('auth');
    }

    /**
     * 違反報告フォームを表示する
     *
     * @param  \App\Service $service 報告対象のサービス
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Service $service)
    {
        $user = Auth::user();

        // 1. 自己報告のチェック: 自分のサービスは報告できない
        if ($user->id === $service->user_id) {
            return redirect()->route('services.show', $service)
                            ->with('error', 'ご自身のサービスを報告することはできません。');
        }

        // 2. 重複報告のチェック: 既に報告済みのサービスは再度報告できない
        if (Report::where('user_id', $user->id)
                     ->where('service_id', $service->id)
                     ->exists()) {
            return redirect()->route('services.show', $service)
                            ->with('error', 'このサービスは既に報告済みです。運営の対応をお待ちください。');
        }

        // フォーム表示
        return view('reports.create', [
            'service' => $service,
        ]);
    }

    /**
     * 新しい違反報告をデータベースに保存する
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Service $service 報告対象のサービス
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Service $service)
    {
        $user = Auth::user();

        // 1. 事前チェック
        if ($user->id === $service->user_id) {
            return back()->with('error', 'ご自身のサービスを報告することはできません。');
        }

        // 2. バリデーション
        // 【修正点】バリデーションキーをDBカラム名 'details' に合わせる
        $validatedData = $request->validate([
            'details' => ['required', 'string', 'max:255'],
        ]);

        // 重複報告チェック
        if (Report::where('user_id', $user->id)->where('service_id', $service->id)->exists()) {
            return back()->with('error', 'このサービスは既に報告済みです。運営の対応をお待ちください。');
        }

        // トランザクション開始
        DB::beginTransaction();

        try {
            // 3. Reportの作成と保存
            $report = new Report();
            $report->user_id = $user->id;
            $report->service_id = $service->id;
            // 【修正点】代入するプロパティ名も 'details' に合わせる
            $report->details = $validatedData['details']; 
            $report->save();

            DB::commit();

            // 成功メッセージと共に投稿詳細画面に戻る
            return redirect()->route('services.show', $service)
                             ->with('success', 'ご報告ありがとうございました。運営が内容を確認します。');

        } catch (Exception $e) {
            DB::rollBack();

            // エラーロギング
            Log::error('Report store failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'service_id' => $service->id,
            ]);

            return redirect()->route('services.show', $service)
                             ->with('error', '報告の送信中にエラーが発生しました。再度お試しください。');
        }
    }
}