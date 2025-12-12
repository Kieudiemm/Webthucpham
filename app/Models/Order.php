<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'order';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'order_note',
        'order_status',
        'order_date',
        'ship',
        'coupon',
        'total'
    ];
}
