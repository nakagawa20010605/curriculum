/**
 * サービス投稿の「いいね」機能を実現するためのAjax処理
 * Laravel 6 環境に対応
 */
document.addEventListener('DOMContentLoaded', () => {
    // CSRFトークンをDOMから取得
    // Bladeファイルで <meta name="csrf-token" content="{{ csrf_token() }}"> が定義されていることが前提
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 全てのいいねボタンを取得
    const likeButtons = document.querySelectorAll('.like-button');

    // 二重送信防止のためのクラス名 (Tailwind CSS)
    const LOADING_CLASS = 'opacity-50 cursor-not-allowed';

    likeButtons.forEach(button => {
        // 初期状態に基づいて、いいねボタンのデータ属性にリスナーを登録
        // 注意: イベントリスナーは DOMContentLoaded で登録すれば、動的に追加された要素には効かないが、
        // 今回はページロード時に存在するボタンに対してのみ動作させる想定とする。
        button.addEventListener('click', async (event) => {
            // クリックされたボタン要素
            const btn = event.currentTarget;
            // HTMLの data-service-id 属性からサービスIDを取得
            const serviceId = btn.dataset.serviceId;
            const icon = btn.querySelector('.like-icon'); // アイコン要素
            const countElement = btn.querySelector('.like-count'); // いいね数要素
            
            if (!serviceId) {
                console.error('Service IDが見つかりません。HTMLの data-service-id 属性を確認してください。');
                return;
            }

            // 二重送信防止のチェック
            if (btn.classList.contains(LOADING_CLASS)) {
                return;
            }
            btn.classList.add(LOADING_CLASS);

            try {
                // LikeController@toggle へのPOSTリクエストを実行
                const response = await fetch(`/services/${serviceId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // CSRFトークンをヘッダーに含める
                        'X-CSRF-TOKEN': csrfToken, 
                        'Accept': 'application/json',
                    },
                    // POSTリクエストだが、ボディが空でもJSON形式で送信
                    body: JSON.stringify({}) 
                });

                if (!response.ok) {
                    // HTTPエラーハンドリング (4xx, 5xx)
                    const errorData = await response.json();
                    throw new Error(`HTTPエラー！ステータス: ${response.status} - ${errorData.message || '不明なエラー'}`);
                }

                const data = await response.json();
                
                if (data.status === 'success') {
                    // 1. data-is-liked 状態の更新 (★★★ 最重要: CSSで見た目が自動で切り替わる) ★★★
                    const newIsLiked = data.isLiked;
                    btn.dataset.isLiked = newIsLiked ? 'true' : 'false';

                    // 2. いいね数の更新
                    if (countElement) {
                        countElement.textContent = data.likeCount;
                    }
                    
                    // 3. アイコンのポップアニメーション (視覚的なフィードバックのみ)
                    if (icon) {
                        // アイコンのクラス操作は不要。data-is-liked属性の更新でCSSが自動適用される。
                        // 視覚的なフィードバックとして、アニメーションだけ実行する。
                        icon.style.transform = 'scale(1.2)';
                        setTimeout(() => icon.style.transform = 'scale(1)', 150);
                    }
                    
                } else if (data.status === 'error') {
                    // コントローラで定義されたエラーメッセージを表示
                    console.error(`いいねエラー: ${data.message}`);
                    // TODO: ユーザーにカスタムモーダルなどで通知する処理を追加
                }

            } catch (error) {
                console.error('いいね処理中に予期せぬエラーが発生しました:', error);
                // TODO: ユーザーにカスタムモーダルなどで通知する処理を追加
            } finally {
                // ローディング状態の解除
                btn.classList.remove(LOADING_CLASS);
            }
        });
    });
});