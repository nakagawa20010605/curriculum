<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Service;
use App\ServiceRequest;
use App\Like;
use App\Report;

class User extends Authenticatable
{
    use Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ★ リレーション定義 ★
    public function services() {
        return $this->hasMany(Service::class);
    }
    
    public function requests() {
        // ServiceRequestモデルを使用
        return $this->hasMany(ServiceRequest::class);
    }
    
    public function likes() {
        return $this->hasMany(Like::class);
    }
    public function reports() {
        return $this->hasMany(Report::class);
    }

    // ===========================================
    // ★ 依頼リクエストへのリレーション (エラー箇所) ★
    // ===========================================
    /**
     * ユーザーが作成した依頼（リクエスト）を取得します。
     */
    public function requestsMade()
    {
        // ServiceRequestモデルを使用し、外部キー 'user_id' を明示的に指定
        return $this->hasMany(ServiceRequest::class, 'user_id');
    }

    public function requestsReceived()
    {
        return $this->hasManyThrough(
        ServiceRequest::class, // 取得したい最終モデル
        Service::class,        // 中間モデル（サービス）
        'user_id',             // Service の外部キー（投稿者のID）
        'service_id',          // ServiceRequest の外部キー（サービスID）
        'id',                  // User のローカルキー
        'id'                   // Service のローカルキー
        );
    }

    /**
     * このユーザーが「いいね」したサービスとのリレーション (多対多 - belongsToMany)
     * Serviceモデルのインスタンスを直接取得したい場合に便利
     */
    public function likedServices()
    {
        return $this->belongsToMany(\App\Service::class, 'likes', 'user_id', 'service_id')->withTimestamps();
    }
}