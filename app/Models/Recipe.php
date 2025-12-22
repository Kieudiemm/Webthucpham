<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    // Khai báo kết nối và collection (tên bảng trong Mongo)
    protected $connection = 'mongodb';
    protected $collection = 'recipes'; 

    protected $fillable = [
        'user_id',
        'dish_name',
        'dish_image',
        'people_count',
        'budget',
        'dish_type',
        'special_request',
        'ingredients_json',
        'cooking_instructions',
    ];

    protected $casts = [
        'ingredients_json' => 'array',
        'people_count' => 'integer',
    ];
}