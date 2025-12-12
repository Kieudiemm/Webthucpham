<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    // ============================
    //   QUẢN LÝ ĐƠN HÀNG
    // ============================

    public function manage_order(){

        // Lấy đơn hàng + thông tin khách hàng (MongoDB không join được)
        $orders = Order::orderBy('_id','desc')->get();

        foreach ($orders as $order) {
            $order->customer = Customer::where('_id', $order->customer_id)->first();
        }

        return view('index_Admin')->with(
            'admin.manage_order',
            view('admin.manage_order')->with('all_order', $orders)
        );
    }

    // ============================
    //   DUYỆT ĐƠN
    // ============================

    public function handle_product($id){

        // Update trạng thái đơn
        Order::where('_id', $id)->update([
            'order_status' => 1
        ]);

        $carts = session()->get('cart');

        // Lấy thông tin đơn + khách hàng
        $order_details = OrderDetail::where('order_id', $id)->get();
        $order = Order::where('_id', $id)->first();
        $customer = Customer::where('_id', $order->customer_id)->first();

        // Gửi email
        Mail::send('pages.Product.email', compact('customer','carts','order','order_details'), function ($message) use ($customer) {
            $message->from('nghiantk.21it@vku.udn.vn', 'Shop D&N');
            $message->to($customer->email, $customer->email);
            $message->subject('Thông báo đặt hàng');
        });

        // Trừ số lượng sản phẩm
        foreach ($carts as $product_id => $item) {
            $product = Product::where('product_id', $product_id)->first();
            if ($product) {
                $new_quantity = $product->quantity - $item['quantity'];
                Product::where('product_id', $product_id)->update([
                    'quantity' => $new_quantity
                ]);
            }
        }

        return Redirect::to('manage-order');
    }

    // ============================
    //   XEM ĐƠN HÀNG
    // ============================

    public function view_order($id){

        $order_details = OrderDetail::where('order_id', $id)->get();
        $order = Order::where('_id', $id)->first();
        $customer = Customer::where('id_user', $order->customer_id)->first();
        $order_status = $order->order_status;

        return view('admin.view_order')->with(compact(
            'order_details',
            'customer',
            'order',
            'order_status'
        ));
    }

    // ============================
    //   IN HÓA ĐƠN
    // ============================

    public function receipt($id){

        $order_details = OrderDetail::where('order_id', $id)->get();
        $order = Order::where('_id', $id)->first();
        $customer = Customer::where('id_user', $order->customer_id)->first();
        $order_status = $order->order_status;

        return view('admin.Receipt')->with(compact(
            'order_details',
            'customer',
            'order',
            'order_status'
        ));
    }
}
