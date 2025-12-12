<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Wishlist extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id'
    ];
}
