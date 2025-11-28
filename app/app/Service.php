<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    public function likes() {
        return $this->hasMany(Like::class);
    }
    public function reports() {
        return $this->hasMany(Report::class);
    }
}

