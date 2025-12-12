<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class OrderDetail extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'order_details';

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity'
    ];
}
