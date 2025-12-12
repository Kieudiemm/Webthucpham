<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Bạn cần đăng nhập'], 401);
        }

        $userId = Auth::id();
        $productId = $request->product_id;

        // Kiểm tra đã tồn tại chưa
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            return response()->json(['status' => 'exists', 'message' => 'Đã có trong yêu thích']);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json(['status' => 'success', 'message' => 'Đã thêm vào yêu thích']);
    }
}
