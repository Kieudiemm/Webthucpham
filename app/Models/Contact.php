<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Contact extends Model
{
    protected $connection = 'mongodb'; // ← Quan trọng
    protected $collection = 'contact'; // ← tên collection trong Mongo (không gọi là table)

    public $timestamps = false;

    protected $fillable = [
        'name_contact',
        'email_contact',
        'title_contact',
        'content_contact'
    ];
}
