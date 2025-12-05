<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Laravel 6ではApp\ServiceRequestに配置します
class ServiceRequest extends Model
{
    const PENDING = 0;       // 保留
    const IN_PROGRESS = 1;   // 承認（進行中）
    const COMPLETED = 2;     // 完了
    const DELETED = 3;       // 削除/キャンセル/拒否

    protected $table = 'requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'service_id',
    'user_id',
    'description',
    'tel',
    'email',
    'deadline',
    'status',
    ];

    /**
     * 依頼対象のサービスとのリレーション
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * 依頼者（Requester）とのリレーション
     * user_id が依頼者を示す
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
