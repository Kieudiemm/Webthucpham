<?php

namespace App\Models;

use Jenssegers\Mongodb\Auth\User as Authenticatable;   // <-- Dùng MongoDB
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $collection = 'user'; // tên collection trong Mongo
    protected $primaryKey = '_id';

    protected $fillable = [ 'Name','Email','Password','level' ];

    public function getAuthPassword()
    {
        return $this->password; //N dùng 'Password' viết hoa
    }

    protected $hidden = [
        'Password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];
}
