<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\MongoCategory;
use App\Models\Blog;
use Illuminate\Support\Facades\Redirect;

class CategoryProduct extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    // Lấy category + blog + hot product
    public function getcategory()
    {
        $blog = Blog::all();
        $hot = MongoCategory::where('hot', 1)->get(); // nếu hot lưu trong mongo
        return view('pages.sanpham', compact('hot', 'blog'));
    }

    public function add_category_product(){
        return view('Admin.add_category_product');
    }

    public function all_category_product(){
        $all_category_product = MongoCategory::paginate(5);
        $manage_category_product = view('admin.all_category_product')
            ->with('all_category_product', $all_category_product);

        return view('index_Admin')->with('admin.all_category_product', $manage_category_product);
    }

    public function edit_category_product($category_product_id){
        $edit_category_product = MongoCategory::where('Category_ID', $category_product_id)->first();
        $manage_category_product = view('admin.edit_category_product')
            ->with('edit_category_product', $edit_category_product);

        return view('index_Admin')->with('admin.edit_category_product', $manage_category_product);
    }

    public function save_category_product(Request $request){

        MongoCategory::create([
            'Category_ID'  => $request->category_product_id,
            'Name'         => $request->category_product_name,
            'desc'         => $request->category_product_desc,
            'status'       => $request->category_product_status,
        ]);

        return Redirect::to('add-category-product');
    }

    public function update_category_product(Request $request, $category_product_id){

        MongoCategory::where('Category_ID', $category_product_id)
            ->update([
                'Name' => $request->category_product_name,
                'desc' => $request->category_product_desc,
            ]);

        return Redirect::to('all-category-product');
    }

    public function delete_category_product($category_product_id){
        MongoCategory::where('Category_ID', $category_product_id)->delete();
        return Redirect::to('all-category-product');
    }
}
