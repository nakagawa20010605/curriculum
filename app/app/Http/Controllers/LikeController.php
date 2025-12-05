<?php

namespace App\Http\Controllers;

use App\Like;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class LikeController extends Controller
{
    public function toggle(Service $service)
    {
        $user = Auth::user();

        if(!$user){
            return response()->json(['success' => false], 403);
        }

        // 既にいいね済みかチェック
        $liked = $service->likes()->where('user_id', $user->id)->exists();

        if($liked){
            // いいね解除
            $service->likes()->detach($user->id);
            $isLiked = false;
        } else {
            // いいね追加
            $service->likes()->attach($user->id);
            $isLiked = true;
        }

        // 最新のいいね数を取得
        $likeCount = $service->likes()->count();

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'likeCount' => $likeCount
        ]);
    }
}