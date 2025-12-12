<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Freeship extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tbl_feeship';

    protected $fillable = [
        'city_code',
        'district_code',
        'ward_code',
        'fee'
    ];

    public $timestamps = false;
}
