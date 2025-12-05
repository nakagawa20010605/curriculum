<?php

namespace App;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User; // Userモデルのインポートを追加
use App\Service; // Serviceモデルのインポートを追加

class Report extends Model
{
    protected $fillable = [
        'user_id',      // 報告者 (Requester)
        'service_id',   // 報告対象のサービス (Target Service)
        'details',  // 詳細な説明
    ];
    
    /**
     * 報告したユーザー (報告者) とのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 報告対象の投稿 (サービス) とのリレーション
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
