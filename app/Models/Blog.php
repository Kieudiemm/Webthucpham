<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Blog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blog';

    protected $fillable = [
        'BlogName',
        'BlogImg',
        'BlogContent',
        'Created_at'
    ];
}
