<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoCategory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'category';


    protected $fillable = [
        'Category_ID',
        'Name',
        'desc',
        'status',
        'created_at',
        'updated_at'
    ];
}
