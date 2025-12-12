<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mongodb';  // kết nối mongo
    protected $collection = 'customer'; // tên collection

    protected $fillable = [
        'id_user',
        'FullName',
        'Name',
        'email',
        'Address',
        'phone',
        'note'
    ];
}
