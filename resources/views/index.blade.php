<!DOCTYPE html>
<html lang="en">
<head>
    <title>D&N</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('Asset/css/open-iconic-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/icomoon.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('Asset/css/chat.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Load jQuery CHỈ 1 LẦN -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

</head>

<body class="goto-here">
    @include('header')

    <div class="content">
        @yield('content')
    </div>

    @include('footer')
    @include('chat_ai')


    <!-- JS -->
    <script src="{{ asset('Asset/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('Asset/js/popper.min.js') }}"></script>
    <script src="{{ asset('Asset/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Asset/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('Asset/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('Asset/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('Asset/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('Asset/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('Asset/js/aos.js') }}"></script>
    <script src="{{ asset('Asset/js/jquery.animateNumber.min.js') }}"></script>
    <script src="{{ asset('Asset/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('Asset/js/scrollax.min.js') }}"></script>

    <!-- <script src="{{ asset('Asset/js/google-map.js') }}"></script> -->

    <script src="{{ asset('Asset/js/main.js') }}"></script>
    <script src="{{ asset('Asset/js/onclick.js') }}"></script>
    <script src="{{ asset('Asset/js/cart.js') }}"></script>
    <script src="{{ asset('Asset/js/chat.js') }}"></script>

</body>
</html>
