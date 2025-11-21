<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Service;

class Report extends Model
{

    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 'service_id', 'description',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function service() {
        return $this->belongsTo(Service::class);
    }
}
