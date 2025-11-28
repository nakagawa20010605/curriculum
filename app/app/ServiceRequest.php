<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Service;

class ServiceRequest extends Model
{

    protected $fillable = [
        'user_id', 'service_id', 'description', 'tel', 'email', 'deadline', 'status',
    ];

    // ★ リレーション定義 ★
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function service() {
        return $this->belongsTo(Service::class);
    }
}
