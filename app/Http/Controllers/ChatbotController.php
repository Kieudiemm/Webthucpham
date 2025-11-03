<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\Operator;
use Illuminate\Support\Str;


class ChatbotController extends Controller
{
    //Get history
    public function fetchMessages(Request $request)
    {
        if (Auth::check()) {
            $msgs = ChatMessage::where('user_id', Auth::id())->orderBy('created_at')->get();
        } else {
            $token = $request->cookie('chat_token');
            $msgs = $token ? ChatMessage::where('guest_token', $token)->orderBy('created_at')->get() : collect();
        }
        return response()->json($msgs);
    }

    // Send message (lưu tin nhắn người dùng, gọi AI, lưu tin bot reply)
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000',]);
        $userId = Auth::id();

        $guestToken = null;
        if(!$userId) {
            $guestToken = $request->cookie('chat_token');
            if(!$guestToken){
                $guestToken = 'guest_' . Str::random(32);

                cookie()->queue(cookie('chat_token', $guestToken, 60 * 24 * 180));
            }
        }

        // Lưu tin nhắn người dùng đến DB
        $userMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $request -> message,
        ]);

        //prepare prompt
        $product = Product::where('quantity', '>', 0)->get(['Title', 'Price', 'product_desc'])->map(function ($p) {
            return "{$p->Title} - {$p->Price}";
        })->toArray();
        $productList = implode("\n", $product);

        $prompt = "Bạn là trợ lý bán hàng cho website rau củ. Dưới đây là danh sách một số sản phẩm hiện có: \n$productList\n
        Hãy trả lời ngắn ngọn, trung thực, chỉ dùng thông tin trong danh sách sản phẩm nếu cần.";

        $history = ChatMessage::query()
        ->where(function($q) use ($userId, $guestToken) {
            if ($userId) {
                $q->where('user_id',$userId);
            } else {
                $q->where('guest_token', $guestToken);
            }
        })
        ->latest()
        ->limit(6)
        ->orderBy('created_at','asc')
        ->get();


        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                "role" => $msg->sender === 'user' ? "user" : "model",
                "parts" => [["text" => $msg->message]]
            ];
        }

        $contents[] = [
            "role" => "user",
            "parts" => [["text" => $request->message]]
        ];

        // Call AI(Gemini)
        $aiReplyText = "Xin lỗi, hiện tại Ai chưa được cấu hình";
        if (env('GOOGLE_GEMINI_API_KEY')) {
            try {
                $url_apikey = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
                $payload = [
                    "systemInstruction" => [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                        ],
                        "contents" => $contents
                    ];

                    //Call API Gemini
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Goog-Api-Key' => env('GOOGLE_GEMINI_API_KEY'),
                    ])->post($url_apikey,$payload);

                    if($response->successful()) {
                        $data = $response->json();
                        $aiReplyText = $data['candidates'][0]['content']['parts'][0]['text']
                            ?? "Xin lỗi, tôi chưa hiểu câu hỏi.";
                    } else {
                        $aiReplyText = "Xin lỗi, AI không thể xử lý lúc này.";
                        Log::error('AI API error', ['response'=> $response->json()]);
                    }
            } catch (\Throwable $e) {
                Log::error('API call error: ' . $e->getMessage());
                $aiReplyText = "Xin lỗi, hiện tại không thể kết nối AI";
            }
        }

        //Save bot reply
        $botMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $aiReplyText,
        ]);
         // Return 2 message created
         return response()->json([
            'user' => $userMsg,
            'bot' => $botMsg,
         ]);

    }

}
