<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Coupon extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tbl_coupon';

    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'coupon_time',
        'coupon_number',
        'coupon_condition'
    ];
}
