<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    // Lấy lịch sử chat
    public function fetchMessages(Request $request)
    {
        if (Auth::check()) {
            $msgs = ChatMessage::where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $token = $request->cookie('chat_token');
            $msgs = $token
                ? ChatMessage::where('guest_token', $token)
                    ->orderBy('created_at', 'asc')
                    ->get()
                : collect();
        }

        return response()->json($msgs);
    }

    // Gửi tin nhắn + gọi AI
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $userId = Auth::id();
        $guestToken = null;

        // Xử lý guest token
        if (!$userId) {
            $guestToken = $request->cookie('chat_token');

            if (!$guestToken) {
                $guestToken = 'guest_' . Str::random(32);
                cookie()->queue(cookie('chat_token', $guestToken, 60 * 24 * 180)); // 180 ngày
            }
        }

        // Lưu tin nhắn người dùng
        $userMsg = ChatMessage::create([
            'user_id'     => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender'      => 'user',
            'message'     => $request->message,
        ]);

        // Chuẩn bị prompt sản phẩm
        $product = Product::where('quantity', '>', 0)
            ->get(['Title', 'Price', 'product_desc'])
            ->map(function ($p) {
                return "{$p->Title} - {$p->Price}";
            })
            ->toArray();

        $productList = implode("\n", $product);

        $prompt = "Bạn là trợ lý bán hàng cho website rau củ.
        Danh sách sản phẩm hiện có:
        $productList
        Hãy trả lời ngắn gọn, đúng thông tin.";

        // Lấy lịch sử chat gần nhất (MongoDB)
        $history = ChatMessage::query()
            ->where(function ($q) use ($userId, $guestToken) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('guest_token', $guestToken);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->sortBy('created_at'); // sắp xếp lại ASC sau khi limit

        // Định dạng contents gửi API
        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                "role" => $msg->sender === 'user' ? "user" : "model",
                "parts" => [
                    ["text" => $msg->message]
                ],
            ];
        }

        $contents[] = [
            "role" => "user",
            "parts" => [
                ["text" => $request->message]
            ],
        ];

        // Gọi API Gemini
        $aiReplyText = "Xin lỗi, AI chưa được cấu hình.";

        if (env('GOOGLE_GEMINI_API_KEY')) {
            try {
                $response = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'X-Goog-Api-Key'=> env('GOOGLE_GEMINI_API_KEY'),
                ])->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                    [
                        "systemInstruction" => [
                            "parts" => [["text" => $prompt]]
                        ],
                        "contents" => $contents
                    ]
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $aiReplyText = $data['candidates'][0]['content']['parts'][0]['text']
                        ?? "Xin lỗi, tôi chưa hiểu câu hỏi.";
                } else {
                    Log::error("Gemini API error", ['response' => $response->body()]);
                    $aiReplyText = "Xin lỗi, AI không phản hồi.";
                }
            } catch (\Throwable $e) {
                Log::error("Gemini Exception: " . $e->getMessage());
                $aiReplyText = "Xin lỗi, không thể kết nối AI.";
            }
        }

        // Lưu trả lời bot vào MongoDB
        $botMsg = ChatMessage::create([
            'user_id'     => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender'      => 'bot',
            'message'     => $aiReplyText,
        ]);

        return response()->json([
            'user' => $userMsg,
            'bot'  => $botMsg,
        ]);
    }
}
