<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;

class AdminController extends Controller
{
    public function show_dashboard()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $currentYear = date("Y");
        $currentMonth = date("m");
        $today = date("Y-m-d");

        // ---------------- COUNT ----------------
        $product = Product::count();
        $user = User::count();

        // ---------------- ORDER PER MONTH ----------------
        $orderCounts = [];

        for ($m = 1; $m <= 12; $m++) {

            $start = "$currentYear-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01";
            $end   = "$currentYear-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-31";

            $orderCounts[$m] = Order::where('order_date', '>=', $start)
                                    ->where('order_date', '<=', $end)
                                    ->count();
        }

        // ---------------- PRICE IN DECEMBER ----------------
        $price = OrderDetail::where('date', '>=', "$currentYear-12-01")
                            ->where('date', '<=', "$currentYear-12-31")
                            ->sum('price');

        // ---------------- COUPON IN DECEMBER ----------------
        $coupon = Order::where('order_date', '>=', "$currentYear-12-01")
                       ->where('order_date', '<=', "$currentYear-12-31")
                       ->sum('coupon');

        // ---------------- CATEGORY COUNT ----------------
        $ct1 = OrderDetail::where('Category_id', 1)->count();
        $ct2 = OrderDetail::where('Category_id', 2)->count();
        $ct3 = OrderDetail::where('Category_id', 3)->count();
        $ct4 = OrderDetail::where('Category_id', 4)->count();

        // ---------------- TODAY ----------------
        $order_date = Order::where('order_date', $today)->count();
        $price_date = OrderDetail::where('date', $today)->sum('price');
        $coupon_date = Order::where('order_date', $today)->sum('coupon');

        // ---------------- LAST 4 DAYS ----------------
        $dates = [];
        for ($i = 1; $i <= 4; $i++) {
            $dates[$i] = date('Y-m-d', strtotime("-$i days"));
        }

        return view('Admin.dashboard', [
            'product' => $product,
            'user' => $user,
            'price' => $price,

            // Orders per month
            'order1' => $orderCounts[1],
            'order2' => $orderCounts[2],
            'order3' => $orderCounts[3],
            'order4' => $orderCounts[4],
            'order5' => $orderCounts[5],
            'order6' => $orderCounts[6],
            'order7' => $orderCounts[7],
            'order8' => $orderCounts[8],
            'order9' => $orderCounts[9],
            'order10' => $orderCounts[10],
            'order11' => $orderCounts[11],
            'order' => $orderCounts[12],

            // Today
            'order_date' => $order_date,
            'price_date' => $price_date,
            'coupon_date' => $coupon_date,

            // Month coupon
            'coupon' => $coupon,

            // Date
            'date' => $today,
            'date_s' => date("d"),

            // Last 4 days
            'date_1' => $dates[1],
            'order_date_1' => Order::where('order_date', $dates[1])->count(),

            'date_2' => $dates[2],
            'order_date_2' => Order::where('order_date', $dates[2])->count(),

            'date_3' => $dates[3],
            'order_date_3' => Order::where('order_date', $dates[3])->count(),

            'date_4' => $dates[4],
            'order_date_4' => Order::where('order_date', $dates[4])->count(),

            // Category count
            'ct1' => $ct1,
            'ct2' => $ct2,
            'ct3' => $ct3,
            'ct4' => $ct4,
        ]);
    }

    public function logout()
    {
        Session::put('admin_name', null);
        Session::put('admin_id', null);
        return Redirect::to('/admin');
    }
}
