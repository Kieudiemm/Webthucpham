<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\MongoCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Trang checkout
     */
    public function getcheckout()
    {
        // Lấy category, city, province, wards từ Mongo
        $category = MongoCategory::all();
        $carts = session()->get('cart');
        $city = json_decode(file_get_contents('https://provinces.open-api.vn/api/?depth=1'));
        $province = json_decode(file_get_contents('https://provinces.open-api.vn/api/p/'));
        $wards = json_decode(file_get_contents('https://provinces.open-api.vn/api/w/'));


        return view('pages.Product.checkout', compact('category', 'carts', 'city', 'province', 'wards'));
    }

    /**
     * Xử lý checkout (Direct)
     */
    public function checkout(Request $request)
    {
        if ($request->Payments == 'Direct') {

            $validator = Validator::make($request->all(), [
                'FullName' => 'required|max:100',
                'Address' => 'required|max:200',
                'phone' => 'required|min:10|max:10',
            ], [
                'FullName.required' => 'Họ và tên là trường bắt buộc',
                'Address.required' => 'Địa chỉ là trường bắt buộc',
                'phone.required' => 'Số điện thoại là trường bắt buộc',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            date_default_timezone_set('Asia/Ho_Chi_Minh');

            $carts = session()->get('cart', []);
            $fee = session()->get('fee', 0);
            $cou = session()->get('cou', null);

            $user = Auth::user();
            $id_user = $user->id;
            $Name = $user->Name ?? ($user->name ?? '');
            $email = $user->Email ?? ($user->email ?? '');

            $Address = $request->Address;
            $adr = session()->get('add', '');
            $phone = $request->phone;
            $note = $request->note;
            $add = trim(implode(', ', array_filter([$Address, $adr])));
            $date = date("Y-m-d");

            // Kiểm tra tồn kho bằng Product model (Mongo)
            foreach ($carts as $product_id => $item) {
                $product = Product::where('product_id', (string)$product_id)->first();
                if (!$product) {
                    Session::flash('soluong', 'Sản phẩm không tồn tại: ' . $product_id);
                    return redirect()->back();
                }
                if ($item['quantity'] > ($product->quantity ?? 0)) {
                    Session::flash('soluong', 'Hiện tại số lượng kho còn: ' . ($product->quantity ?? 0) . '. Vui lòng điều chỉnh.');
                    return redirect()->back()->withInput();
                }
            }

            // Tạo Customer (Mongo)
            $customer = Customer::create([
                'id_user' => $id_user,
                'FullName' => $request->FullName,
                'email' => $email,
                'Address' => $add,
                'phone' => $phone,
                'note' => $note,
                'Name' => $Name,
            ]);

            $c_id = $customer->_id; // _id của Mongo

            // Tạo Order (Mongo)
            $order = Order::create([
                'customer_id' => $c_id,
                'order_note' => $note,
                'ship' => $fee,
                'coupon' => $cou,
                'order_status' => '0',
                'order_date' => $date,
            ]);

            $order_id = $order->_id;

            // Tạo OrderDetail (Mongo) và (tuỳ chọn) giảm tồn kho
            foreach ($carts as $product_id => $item) {
                $pr = Product::where('product_id', (string)$product_id)->first();

                OrderDetail::create([
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'price' => $item['price'],
                    'Name' => $item['name'],
                    'Img' => $item['image'],
                    'quantity' => $item['quantity'],
                    'date' => $date,
                    'Category_id' => $pr->Category_ID ?? null,
                ]);

                // (Tùy bạn muốn) cập nhật lượng tồn kho trong products:
                if ($pr && isset($pr->quantity)) {
                    $newQty = max(0, $pr->quantity - $item['quantity']);
                    $pr->update(['quantity' => $newQty]);
                }
            }

            // Xóa giỏ (nếu muốn)
            // session()->forget('cart');

            return redirect()->route('thanhcong')->with('id', $order_id);
        } else {
            Session::flash('noatm', 'Phương thức thanh toán online chưa được áp dụng');
            return redirect()->back();
        }
    }

    /**
     * Trang thanh cong
     */
    public function getthanhcong()
    {
        $category = MongoCategory::all();
        return view('pages.Product.thanhcong', compact('category'));
    }

    /**
     * Danh sách customer (admin)
     */
    public function all_custommer()
    {
        $cus = Customer::orderBy('_id', 'DESC')->paginate(5);
        return view('Admin.all_custommer')->with(compact('cus'));
    }

    /**
     * Trang Đơn mua của user
     */
    public function getdonmua()
    {
        $id_user = Auth::user()->id;
        $customer = Customer::where('id_user', $id_user)->get();
        $category = MongoCategory::all();

        return view('pages.Product.donmua')->with('category', $category);
    }
}
