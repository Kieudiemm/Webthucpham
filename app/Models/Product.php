<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';   // kết nối mongodb
    protected $collection = 'products';  // tên collection

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'Title',
        'Category_ID',
        'Price',
        'Discount',
        'Thumbnail',
        'quantity',
        'hot',
        'sale',
        'new',
        'product_desc',
        'weight'
    ];
}
