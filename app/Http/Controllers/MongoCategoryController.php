<?php

namespace App\Http\Controllers;

use App\Models\MongoCategory;

class MongoCategoryController extends Controller
{
    public function index()
    {
        // Lấy tất cả dữ liệu
        return MongoCategory::all();
    }

}
