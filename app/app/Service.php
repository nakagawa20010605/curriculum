<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\User;
use App\ServiceRequest;
use App\Like;
use App\Report;

class Service extends Model
{

    protected $fillable = [
        'user_id', 'title', 'amount', 'description', 'image', 'status',
    ];

    // ★ リレーション定義 ★
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function requests() {
        return $this->hasMany(ServiceRequest::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes', 'service_id', 'user_id')->withTimestamps();
    }
   
    /**
     * 特定のユーザーがこのサービスを「いいね」しているかチェックするスコープ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsLikedBy($query, $userId)
    {
        return $query->whereHas('likes', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function reports() {
        return $this->hasMany(Report::class);
    }
}

