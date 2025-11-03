<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chat AI</title>

    {{-- Nếu bạn có CSS riêng --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- chat.js --}}
    <script src="{{ asset('js/chat.js') }}"></script>
</head>
<body>

    {{-- Chat Widget --}}
    <div id="chat-widget">
      <button id="chat-toggle">💬</button>

      <div id="chat-box" class="hidden">
        <div id="chat-header">
          <span>Hỗ trợ trực tuyến</span>
          <button id="chat-close" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;">✖</button>
        </div>

        <div id="chat-messages"></div>

        <div id="chat-input">
          <input type="text" id="message-input" placeholder="Nhập tin nhắn...">
          <button id="send-btn">Gửi</button>
        </div>
      </div>
    </div>

</body>
</html>
