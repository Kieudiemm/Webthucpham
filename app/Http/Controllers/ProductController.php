<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

use App\Models\Product;
use App\Models\MongoCategory;

class ProductController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    // ---------------------- ADD PRODUCT ----------------------

    public function add_product() {
        $cate_product = MongoCategory::orderBy('Category_ID', 'desc')->get();
        return view('Admin.add_product')->with('cate_product', $cate_product);
    }

    // ---------------------- SAVE PRODUCT ----------------------

    public function save_product(Request $request) {

        $data = $request->only([
            'product_id',
            'product_name',
            'product_price',
            'product_desc',
            'product_content',
            'product_cate',
            'product_status',
            'product_discout',
            'product_quantity',
            'hot',
            'sale'
        ]);

        $insert = [
            'product_id'     => $data['product_id'],
            'Title'          => $data['product_name'],
            'Price'          => $data['product_price'],
            'Discount'       => $data['product_discout'],
            'quantity'       => $data['product_quantity'],
            'product_desc'   => $data['product_desc'],
            'product_content'=> $data['product_content'],
            'Category_ID'    => $data['product_cate'],
            'product_status' => $data['product_status'],
            'hot'            => $data['hot'],
            'sale'           => $data['sale']
        ];

        // Upload ảnh
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $new_image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Asset/images'), $new_image);
            $insert['Thumbnail'] = $new_image;
        }

        Product::create($insert);

        Session::put('message','Thêm sản phẩm thành công');
        return Redirect::to('add-product');
    }

    // ---------------------- ALL PRODUCT ----------------------

    public function all_product() {

        $all_product = Product::orderBy('product_id','desc')->paginate(5);

        return view('index_Admin')->with(
            'admin.all_product',
            view('admin.all_product')->with('all_product', $all_product)
        );
    }

    // ---------------------- EDIT PRODUCT ----------------------

    public function edit_product($product_id) {

        $cate_product = MongoCategory::orderBy('Category_ID','desc')->get();

        $edit_product = Product::where('product_id',$product_id)->first();

        return view('index_Admin')->with(
            'admin.edit_product',
            view('admin.edit_product')
                ->with('edit_product',$edit_product)
                ->with('cate_product',$cate_product)
        );
    }

    // ---------------------- UPDATE PRODUCT ----------------------

    public function update_product(Request $request,$product_id) {

        $data = [
            'Title'          => $request->product_name,
            'Price'          => $request->product_price,
            'Discount'       => $request->product_discount,
            'product_desc'   => $request->product_desc,
            'product_content'=> $request->product_content,
            'Category_ID'    => $request->product_cate,
            'product_status' => $request->product_status,
            'hot'            => $request->hot,
            'new'            => $request->new,
            'sale'           => $request->sale,
        ];

        // Upload ảnh
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $new_image = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('Asset/images'), $new_image);
            $data['Thumbnail'] = $new_image;
        }

        Product::where('product_id', $product_id)->update($data);

        return Redirect::to('all-product');
    }

    // ---------------------- DELETE PRODUCT ----------------------

    public function delete_product($product_id){
        Product::where('product_id', $product_id)->delete();
        return Redirect::to('all-product');
    }
}
