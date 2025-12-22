<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MongoCategory;
use App\Models\Recipe;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    /**
     * Hiển thị form gợi ý món ăn.
     */
    public function showForm()
    {
        $category = MongoCategory::all();

        return view('pages.Product.recipe_suggest', [
            'category'     => $category,
            'dish'         => null,
            'suggestions'  => [],
            'errorMessage' => null,
        ]);
    }

    /**
     * Hiển thị danh sách công thức đã hỏi của user.
     */
    public function myRecipes(Request $request)
    {
        $query = Recipe::where('user_id', auth()->id());

        // Tìm kiếm theo tên món
        if ($request->has('search') && $request->search) {
            $query->where('dish_name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo ngân sách
        if ($request->has('budget') && $request->budget) {
            $query->where('budget', $request->budget);
        }

        // Lọc theo loại món
        if ($request->has('dish_type') && $request->dish_type) {
            $query->where('dish_type', $request->dish_type);
        }

        $recipes = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('pages.Product.my_recipes', [
            'recipes' => $recipes,
            'search' => $request->search ?? '',
            'selectedBudget' => $request->budget ?? '',
            'selectedDishType' => $request->dish_type ?? '',
        ]);
    }

    /**
     * Lấy chi tiết công thức (dùng cho AJAX popup).
     */
    public function getRecipeDetail($id)
    {
        try {
            // MongoDB có thể cần convert _id
            $recipe = Recipe::where('_id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$recipe) {
                return response()->json(['error' => 'Công thức không tồn tại'], 404);
            }

            // Convert _id sang string để JSON response
            $recipeData = $recipe->toArray();
            $recipeData['_id'] = (string)$recipe->_id;

            return response()->json([
                'recipe' => $recipeData,
            ]);
        } catch (\Exception $e) {
            Log::error('Get Recipe Detail Error: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi lấy chi tiết công thức'], 500);
        }
    }

    /**
     * Nhận tên món, gọi Gemini sinh gợi ý nguyên liệu.
     */
   public function handleSuggestion(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'dish'   => 'required|string|max:255',
        'people' => 'required|integer|min:1',
        'budget' => 'required|in:thấp,trung bình,cao',
    ]);

    // 2. Lấy dữ liệu từ Request
    $dish = $request->input('dish');
    $people = $request->input('people');
    $budget = $request->input('budget');
    $dishType = $request->input('dish_type');
    $specialRequest = $request->input('special_request');
    
    // 3. Khởi tạo biến mặc định (QUAN TRỌNG: Để tránh lỗi View khi code chạy vào catch)
    $category = MongoCategory::all();
    $errorMessage = null;
    $suggestions = []; // Mảng chứa sản phẩm gợi ý
    $ingredientsData = []; // Mảng chứa dữ liệu lưu vào DB Recipe
    $recipe = null;
    $dishImage = null;
    $cookingInstructions = ''; 

    // 4. Lấy danh sách sản phẩm (TỐI ƯU: Chỉ lấy cột cần thiết để tiết kiệm Token)
    // Lưu ý: Đã thêm cột 'unit' để biết sản phẩm bán theo kg hay cái
    $products = Product::where('quantity', '>', 0)
        ->take(150) // Giới hạn số lượng để tránh quá tải token
        ->get(['product_id', 'Title', 'Price', 'Discount', 'unit']) 
        ->map(fn($p) => [
            'title' => $p->Title,
            'price' => $p->Discount ?: $p->Price,
            'unit'  => $p->unit ?? 'kg', // Mặc định là kg nếu trong DB null
        ]);

    // Tạo chuỗi text danh sách sản phẩm gửi cho AI
    $productListText = $products->map(function ($p) {
        return "- {$p['title']} (Đơn vị bán: {$p['unit']})";
    })->implode("\n");

    try {
        // 5. Cấu hình Prompt cho Gemini
        $systemInstruction = "Bạn là trợ lý đầu bếp thông minh.
        Nhiệm vụ: Gợi ý nguyên liệu từ kho hàng để nấu món '{$dish}' cho {$people} người ăn.
        Ngân sách: {$budget}.
        " . ($dishType ? "Phong cách: {$dishType}. " : "") .
        ($specialRequest ? "Lưu ý: {$specialRequest}. " : "") . "

        Danh sách sản phẩm trong kho (Kèm đơn vị bán):
        {$productListText}

        Yêu cầu Output: Trả về duy nhất 1 JSON object (không markdown), cấu trúc:
        {
            \"ingredients\": [
                {
                    \"title\": \"Tên sản phẩm khớp trong kho\",
                    \"quantity\": số_lượng_cần_dùng (số thực),
                    \"unit\": \"đơn_vị_của_món_ăn (g, kg, ml, quả...)\",
                    \"note\": \"ghi chú ngắn gọn\"
                }
            ],
            \"recipe\": \"<p><strong>Bước 1:</strong>...</p>...\",
            \"dish_image_url\": \"(URL ảnh thực tế món ăn hoặc null)\"
        }
        Lưu ý logic: Nếu món ăn cần 500g thịt, nhưng kho bán theo kg, hãy trả về unit là 'g' để tôi tự quy đổi.";

        $systemInstructionContent = new Content(
            role: Role::MODEL, // Rất quan trọng: Phải là SYSTEM
                parts: [
                    new Part(text: $systemInstruction)
                ]
        );
        // 6. Gọi API Gemini
        // Sử dụng model 1.5-flash hoặc 2.0-flash để tiết kiệm và nhanh
        $result = Gemini::generativeModel(model: 'gemini-2.5-flash') 
            ->withSystemInstruction($systemInstructionContent)
            ->generateContent("Hãy lên thực đơn cho món: {$dish}");

        $text = $result->text();

        // 7. Xử lý JSON trả về (Clean JSON từ Markdown ```json ... ```)
        $decoded = null;
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
        } else {
            $decoded = json_decode($text, true);
        }

        // 8. Mapping dữ liệu AI với Database
        if (is_array($decoded) && isset($decoded['ingredients'])) {
            $dishImage = $decoded['dish_image_url'] ?? null;
            $cookingInstructions = $decoded['recipe'] ?? 'Đang cập nhật công thức...';

            foreach ($decoded['ingredients'] as $item) {
                if (empty($item['title'])) continue;

                // Tìm sản phẩm trong DB (Chính xác hoặc gần đúng)
                $product = Product::where('Title', $item['title'])->first()
                    ?? Product::where('Title', 'like', '%' . $item['title'] . '%')->first();

                if ($product) {
                    // --- LOGIC QUY ĐỔI ĐƠN VỊ (FIX BUG 500g = 500kg) ---
                    $aiQty = (float)($item['quantity'] ?? 1);
                    $aiUnit = strtolower(trim($item['unit'] ?? '')); // Đơn vị AI gợi ý (g, ml)
                    $dbUnit = strtolower(trim($product->unit ?? 'kg')); // Đơn vị Kho bán (kg, l)

                    $cartQuantity = $aiQty; 

                    // Quy đổi Gram -> Kg
                    if (in_array($aiUnit, ['g', 'gram', 'gr']) && $dbUnit == 'kg') {
                        $cartQuantity = $aiQty / 1000;
                    }
                    // Quy đổi Ml -> Lít
                    elseif ($aiUnit == 'ml' && in_array($dbUnit, ['l', 'lít', 'lit'])) {
                        $cartQuantity = $aiQty / 1000;
                    }
                    
                    // Giới hạn số lượng tối thiểu (tránh số 0.005)
                    if ($cartQuantity < 0.01) $cartQuantity = 0.1;

                    // Tạo text hiển thị "Cần dùng: ..."
                    $usageDisplay = $aiQty . ' ' . $item['unit'];

                    // Thêm vào mảng Suggestions (Để hiển thị ra View)
                    $suggestions[] = [
                        'product'          => $product,
                        'quantity'         => $cartQuantity,    // Số lượng ADD VÀO GIỎ (đã quy đổi)
                        'usage'            => $usageDisplay,    // FIX LỖI VIEW: Text hiển thị (VD: 500g)
                        'unit'             => $dbUnit,
                        'note'             => $item['note'] ?? '',
                        'display_quantity' => $cartQuantity . ' ' . $dbUnit // Text hiển thị số lượng mua
                    ];

                    // Thêm vào mảng Data (Để lưu vào DB Recipe)
                    $ingredientsData[] = [
                        'product_id' => $product->product_id,
                        'title'      => $product->Title,
                        'quantity'   => $cartQuantity,
                        'unit'       => $dbUnit,
                        'usage_str'  => $usageDisplay, 
                        'note'       => $item['note'] ?? ''
                    ];
                }
            }
        }

        // 9. Xử lý trường hợp không tìm thấy hoặc lỗi
        if (empty($suggestions)) {
            $errorMessage = 'Rất tiếc, AI không tìm thấy nguyên liệu phù hợp trong kho.';
        } else {
            // Lưu Recipe vào Database (History)
            try {
                $recipe = Recipe::create([
                    'user_id'              => auth()->id(),
                    'dish_name'            => $dish,
                    'dish_image'           => $dishImage,
                    'people_count'         => (int)$people,
                    'budget'               => $budget,
                    'dish_type'            => $dishType,
                    'special_request'      => $specialRequest,
                    'ingredients_json'     => $ingredientsData, // Lưu mảng JSON đã xử lý
                    'cooking_instructions' => $cookingInstructions,
                ]);
            } catch (\Exception $e) {
                // Lỗi lưu DB không quan trọng, log lại và bỏ qua để user vẫn xem được kết quả
                Log::error('Recipe Save Error: ' . $e->getMessage());
            }
        }

    } catch (\Exception $e) {
        // 10. Catch lỗi chung (Quota, API Error, Code Error)
        Log::error('Gemini Error: ' . $e->getMessage());
        
        // Thông báo thân thiện tùy loại lỗi
        if (strpos($e->getMessage(), '429') !== false) {
            $errorMessage = 'Hệ thống đang quá tải. Vui lòng thử lại sau 30 giây.';
        } else {
            $errorMessage = 'Có lỗi khi kết nối với đầu bếp AI. Vui lòng thử lại.';
        }
        
        // Đảm bảo biến $cookingInstructions không null để View không lỗi
        if (!$cookingInstructions) $cookingInstructions = '';
    }

    // 11. Trả về kết quả
    if ($request->ajax() || $request->expectsJson()) {
        return response()->json([
            'dish'           => $dish,
            'dishImage'      => $dishImage,
            'people'         => $people,
            'errorMessage'   => $errorMessage,
            'hasSuggestions' => !empty($suggestions),
            'recipe'         => $recipe,
            'cookingInstructions' => $cookingInstructions,
            'html'           => view('pages.Product.partials.recipe_suggest_results', compact(
                'dish', 'dishImage', 'people', 'suggestions', 'errorMessage', 'recipe', 'cookingInstructions'
            ))->render(),
        ]);
    }

    return view('pages.Product.recipe_suggest', compact('category', 'dish', 'dishImage', 'people', 'suggestions', 'errorMessage', 'recipe', 'cookingInstructions'));
}

    /**
     * Thêm nguyên liệu đã chọn vào giỏ, có thể chuyển thẳng sang thanh toán.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'items'               => 'required|array',
            'items.*.product_id'  => 'required',
            'items.*.quantity'    => 'required|integer|min:1',
            'action'              => 'required|in:cart,buy_now',
        ]);

        $items  = $request->input('items', []);
        $action = $request->input('action');

        $cart = session()->get('cart', []);

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $qty       = (int) ($item['quantity'] ?? 1);

            if (!$productId || $qty < 1) {
                continue;
            }

            $product = Product::where('product_id', $productId)->first();
            if (!$product) {
                continue;
            }

            $price = $product->Discount ?: $product->Price;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $qty;
            } else {
                $cart[$productId] = [
                    'name'     => $product->Title,
                    'price'    => $price,
                    'quantity' => $qty,
                    'image'    => $product->Thumbnail,
                ];
            }
        }

        session()->put('cart', $cart);

        if ($action === 'buy_now') {
            return redirect()->route('thanhtoan')->with('success', 'Đã thêm nguyên liệu và chuyển đến thanh toán.');
        }

        return redirect()->route('giohang')->with('success', 'Đã thêm nguyên liệu vào giỏ hàng.');
    }
}
