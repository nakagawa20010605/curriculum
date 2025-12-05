<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(){
        $services = Service::where('status', '!=', 3)->orderBy('created_at', 'desc')->paginate(10);
        return view('home', compact('services'));
    }

    public function show(Service $service){
        // ステータス3（削除済）は表示しない
    if ($service->status === 3) {
        abort(404);
    }

    $userId = Auth::id();

    // ログインしている場合のみ判定
    $isLiked = $userId 
        ? $service->likes()->where('user_id', $userId)->exists()
        : false;

    // いいね数
    $likeCount = $service->likes()->count();
    return view('services.show', compact('service', 'isLiked', 'likeCount'));
    }
    

    public function create()
    {
        return view('services.create');
    }

    /**
     * 新しいサービスをストレージに保存 ★新規投稿の完了処理の中核★
     */
    public function store(Request $request)
    {
        // 1. バリデーション
        $validatedData = $request->validate([
            'title' => 'required|max:100', // 必須、最大100文字
            'amount' => 'required|integer|min:100|max:1000000', // 必須、整数、100円以上100万円以下
            'description' => 'required|max:5000', // 必須、最大5000文字
            'image' => 'nullable|image|max:2048', // 任意、画像ファイル、最大2MB (2048KB)
        ]);

        // トランザクション開始
        DB::beginTransaction();

        try {
            $imagePath = null;
            
            // 2. 画像アップロード
            if ($request->hasFile('image')) {
                // ファイルを 'public/services' ディレクトリに保存
                $imagePath = $request->file('image')->store('services', 'public');
            }

            // 3. データ保存
            $service = new Service();
            $service->user_id = Auth::id(); // 現在ログインしているユーザーのIDを設定
            $service->title = $validatedData['title'];
            $service->amount = $validatedData['amount'];
            $service->description = $validatedData['description'];
            $service->image = $imagePath; // ファイルパスを保存
            $service->status = 1; // ステータスを「公開中(1)」として設定
            
            $service->save(); // ★★★ データベースへの保存実行 ★★★

            DB::commit(); // トランザクションコミット
            
            // 4. 投稿詳細ページへリダイレクトし、成功メッセージを付与
            return redirect()->route('services.show', $service);

        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生した場合はロールバック
            
            // 画像がアップロードされていた場合、ストレージからも削除
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            // ログにエラーを記録し、フォームに戻す
            \Log::error('Service store failed: ' . $e->getMessage());

            // 失敗メッセージをフラッシュし、入力値を保持してフォームに戻る
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'サービスの投稿中にエラーが発生しました。再度お試しください。');
        }
    }

    public function edit(Service $service)
    {
        // 権限チェック：投稿者本人以外は編集不可
        if (Auth::id() !== $service->user_id) {
            return redirect()->route('services.show', $service)->with('error', 'この投稿を編集する権限がありません。');
        }

        // 編集ビューを返す
        return view('services.edit', compact('service'));
    }

    /**
     * サービスを更新
     * @param \Illuminate\Http\Request $request
     * @param \App\Service $service
     */
    public function update(Request $request, Service $service)
    {
        // 1. 権限チェック：投稿者本人以外は更新不可
        if (Auth::id() !== $service->user_id) {
            return redirect()->route('services.show', $service)->with('error', 'この投稿を更新する権限がありません。');
        }
        
        // 2. バリデーション
        $validatedData = $request->validate([
            'title' => 'required|max:100',
            'amount' => 'required|integer|min:100|max:1000000',
            'description' => 'required|max:5000',
            'image' => 'nullable|image|max:2048', 
        ]);

        // トランザクション開始
        DB::beginTransaction();

        try {
            $imagePath = $service->image; // 既存の画像パスを保持

            // 3. 画像の処理
            
            // a) 画像削除のチェックボックスがオンの場合
            if ($request->filled('delete_image') && $imagePath) {
                // 古いファイルをストレージから削除
                Storage::disk('public')->delete($imagePath); 
                $imagePath = null; // DBに保存するパスもnullにする
            }
            
            // b) 新しい画像がアップロードされた場合
            if ($request->hasFile('image')) {
                // 古い画像があれば削除（チェックボックスに関わらず、上書きなので削除）
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                // 新しい画像を保存し、パスを更新
                $imagePath = $request->file('image')->store('services', 'public');
            }

            // 4. データ更新
            $service->title = $validatedData['title'];
            $service->amount = $validatedData['amount'];
            $service->description = $validatedData['description'];
            $service->image = $imagePath; // 更新されたパス（nullまたは新しいパス）を設定
            
            $service->save(); // データベースへの保存実行

            DB::commit(); // コミット

            // 成功時: 投稿詳細ページへリダイレクトし、成功メッセージを付与
            return redirect()->route('services.show', $service);

        } catch (\Exception $e) {
            DB::rollBack();

            // ログにエラーを記録
            \Log::error('Service update failed: ' . $e->getMessage());

            // 失敗時: フォームに戻り、エラーメッセージを付与
            return redirect()->back()
                             ->withInput()
                             ->with('error', '投稿の更新中にエラーが発生しました。再度お試しください。');
        }
    }

    /**
     * サービスを削除（ソフトデリートとしてステータスを更新）
     * @param \App\Service $service
     */
    public function destroy(Service $service)
    {
        // 1. 権限チェック：投稿者本人以外は削除不可
        if (Auth::id() !== $service->user_id) {
            return redirect()->route('services.show', $service)->with('error', 'この投稿を削除する権限がありません。');
        }

        // 2. ステータスを「削除済（3）」に更新
        DB::beginTransaction();
        try {
            $service->status = 3; // 3: 削除済
            $service->save();

            DB::commit();
            
            // ホーム画面にリダイレクト
            return redirect()->route('home');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Service destroy failed: ' . $e->getMessage());
            
            return redirect()->back()
                             ->with('error', '投稿の削除中にエラーが発生しました。再度お試しください。');
        }
    }
}