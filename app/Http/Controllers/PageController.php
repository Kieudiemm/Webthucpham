<?php

namespace App\Http\Controllers;

use App\Models\Product as ModelsProduct;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\City;
use App\Models\Province;
use App\Models\Wards;
use App\Models\Freeship;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Session;
use App\Models\Coupon;
use App\Models\MongoCategory;
use App\Models\Blog;
use App\Models\Contact;



use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
class PageController extends Controller
{

    public function getindex()
    {
        $blog = Blog::orderBy('_id', 'desc')->get()->map(function($item){

        // Nếu BlogImg là array → lấy phần tử đầu tiên
            if (is_array($item->BlogImg)) {
                $item->BlogImg = $item->BlogImg[0] ?? '';
            }

            return $item;
        });

        $hot = ModelsProduct::where('hot',1)->orderby('id','desc')->take(12)->get();
        $category = MongoCategory::all();

        return view('pages.trangchu', compact('hot', 'blog', 'category'));
    }

    public function gettimkiem(Request $request)

    {
        $key= $request->key;

       $search = ModelsProduct::where('Title', 'like', "%$key%")->get();
        $category = MongoCategory::all();
        return view('pages.Product.search', compact('search', 'category', 'key' ));
    }

    public function getloc(Request $request)

    {
        $sort = $request->sort;

        if ($sort == 'tang_dan') {
            $loc = ModelsProduct::orderBy('Price', 'ASC')->paginate(16);

        } else if ($sort == 'giam_dan') {
            $loc = ModelsProduct::orderBy('Price', 'DESC')->paginate(16);

        } else if ($sort == 'kytu_az') {
            $loc = ModelsProduct::orderBy('Title', 'ASC')->paginate(16);

        } else {
            $loc = ModelsProduct::orderBy('Title', 'DESC')->paginate(16);
        }

        $category = MongoCategory::all();

        return view('pages.Product.loc', compact('loc', 'category'));
    }

    public function getmuangay()
    {
        $product = ModelsProduct::orderby('id','desc')->paginate(20);
        $category = MongoCategory::all();
        return view('pages.muangay', compact('product','category'));
    }
     public function getgioithieu()
    {
        $category = MongoCategory::all();
        return view('pages.gioithieu', compact('category'));
    }
    public function gettintuc()

    {
        $category = MongoCategory::all();
        $blog = Blog::orderBy('_id', 'desc')->paginate(5);

        return view('pages.tintuc', compact('blog', 'category'));

    }
    public function getlienhe()
    {
        $category = MongoCategory::all();
        return view('pages.lienhe', compact('category'));
    }

    public function postlienhe(Request $request)
    {

        $allRequest  = $request->all();
        $name_contact  = $allRequest['ten'];
        $email_contact = $allRequest['email'];
        $title_contact = $allRequest['tieude'];
        $content_contact = $allRequest['tinnhan'];
         $dataInsert = array(
            'name_contact'  => $name_contact,
            'email_contact' => $email_contact,
            'title_contact' =>$title_contact,
            'content_contact' => $content_contact,


        );
        $insertData = DB::table('contact')->insert($dataInsert);
        $category = MongoCategory::all();
        return view('pages.lienhe', compact('category'));

    }
    public function postcontact_feedback(Request $request)
    {



        Mail::send('pages.Email.lienhe', [
            'name'  => $request->name,
            'content' => $request->content,

        ], function ($message) use($request) {
            $message->from('nghiantk.21it@vku.udn.vn','Shop D&N');
            $message->to( $request->email,$request->name);
            $message->subject('Liên hệ');
        });
        return view('Admin.contact_feedback');
    }

    public function getalllienhe()
    {
        $con = Contact::orderBy('_id', 'desc')->paginate(5);
        return view('Admin.all_contact', compact('con'));
    }



    public function  getcontact_feedback(){
        return view('Admin.contact_feedback');
    }

    public function getyeuthich()
    {
        return view('pages.product.yeuthich');
    }
        public function getthanhtoan()
    {

        return view('pages.product.thanhtoan');
    }
        public function getchitietsanpham($id)
        {
            $sanpham = ModelsProduct::where('product_id', (string)$id)->first();

            $namecategory = MongoCategory::where('Category_ID', (string)$sanpham->Category_ID)->first();

            // Lấy tất cả danh mục
            $category = MongoCategory::all();

            // Sản phẩm HOT
            $new = ModelsProduct::where('hot', 1)->orderBy('id', 'desc')->paginate(12);

            return view('pages.product.chitietsanpham', compact('category', 'sanpham', 'new', 'namecategory'));

        }

    public function getsanpham($id)
    {
        // Lấy danh mục theo Category_ID (string)
        $namecategory = MongoCategory::where('Category_ID', (string)$id)->first();

        // Lấy tất cả danh mục
        $category = MongoCategory::all();

        // LẤY SẢN PHẨM TỪ MONGO
        $sanpham = ModelsProduct::where('Category_ID', (int)$id)->get();

        return view('pages.sanpham', compact('sanpham', 'category', 'namecategory'));
    }
    public function getaddtocart($id)
{
    $product = ModelsProduct::where('product_id', $id)->first();
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $cart[$id]['quantity'] + 1;
    } else {
        $cart[$id] = [
            'name'     => $product->Title,
            'price'    => $product->Discount,
            'quantity' => 1,
            'image'    => $product->Thumbnail,
        ];
    }

    session()->put('cart', $cart);

    // Tính tổng số lượng hiện tại trong giỏ hàng
    $totalQty = 0;
    foreach ($cart as $item) {
        $totalQty += $item['quantity'];
    }

    return response()->json([
        'code'     => 200,
        'message'  => 'success',
        'cartQty'  => $totalQty,     // TRUYỀN VỀ SỐ LƯỢNG
    ], 200);
}

public function getgiohang()
{
    $category = MongoCategory::all();
    $carts = session()->get('cart',[]);

    // Lấy danh sách tỉnh → huyện → xã từ API
    $json = file_get_contents("https://provinces.open-api.vn/api/?depth=3");
    $locations = json_decode($json, true);

    return view('pages.product.giohang', compact('category', 'carts', 'locations'));
}


public function postgiohang(Request $request)
{
    if ($request->isMethod('post')) {

        $validator = Validator::make($request->all(), [
            'city'     => 'required',
            'province' => 'required',
            'wards'    => 'required',
        ], [
            'city.required'     => 'Trường này là trường bắt buộc',
            'province.required' => 'Trường này là trường bắt buộc',
            'wards.required'    => 'Trường này là trường bắt buộc',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lấy dữ liệu API (tỉnh - huyện - xã)
        $json = file_get_contents("https://provinces.open-api.vn/api/?depth=3");
        $locations = json_decode($json, true);

        $cityCode     = $request->city;
        $districtCode = $request->province;
        $wardCode     = $request->wards;
        $couponCode   = $request->coupon;

        // Tìm theo mã
        $city     = collect($locations)->firstWhere('code', $cityCode);
        $district = collect($city['districts'])->firstWhere('code', $districtCode);
        $ward     = collect($district['wards'])->firstWhere('code', $wardCode);

        // Ghép lại địa chỉ
        $address = $ward['name'] . ' , ' . $district['name'] . ' , ' . $city['name'];
        Session::put('add', $address);

        // Xử lý phí ship (vẫn trong DB Freeship)
        $feeship = Freeship::where('city_code', $cityCode)
                   ->where('district_code', $districtCode)
                   ->where('ward_code', $wardCode)
                   ->first();


        if ($feeship) {
            Session::put('fee', $feeship->fee);
        }

        // Xử lý coupon
        $coupon = Coupon::where('coupon_code', $couponCode)->first();
        if ($coupon) {
            Session::put('cou', $coupon->coupon_number);
        }

        Session::save();

        return redirect()->to('thanhtoan');
    }
}

public function getdeletecart(Request $request)
{
    if ($request->id) {

        $carts = session()->get('cart', []);

        // Xoá sản phẩm
        unset($carts[$request->id]);
        session()->put('cart', $carts);

        // Tính lại tổng tiền
        $total = 0;
        foreach ($carts as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Render lại table
        $html = view('pages.product.component_cart_table', [
            'carts' => $carts,
            'total' => $total
        ])->render();

        // Tính số lượng giỏ hàng
        $totalQty = array_sum(array_column($carts, 'quantity'));

        return response()->json([
            'html'  => $html,
            'qty'   => $totalQty,
            'total' => number_format($total),
            'code'  => 200
        ]);
    }
}


}
