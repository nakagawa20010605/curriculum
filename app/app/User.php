<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Service;
use App\Request;
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
        return $this->hasMany(\App\Models\Service::class);
    }
    public function requests() {
        return $this->hasMany(\App\Models\Request::class);
    }
    public function likes() {
        return $this->hasMany(\App\Models\Like::class);
    }
    public function reports() {
        return $this->hasMany(\App\Models\Report::class);
    }
}
