<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Staff extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'staff';

    public $timestamps = false;

    protected $fillable = [
        'staff_name',
        'staff_phone',
        'staff_address',
        'staff_position',
        'id_user'
    ];
}
