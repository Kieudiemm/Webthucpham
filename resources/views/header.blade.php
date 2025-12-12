@php
    $cartCount = 0;
    if (Session::has('cart')) {
        foreach (Session::get('cart') as $item) {
            $cartCount += $item['quantity'];
        }
    }
@endphp

<div class="container">
    <div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
        <div class="col-lg-12 d-block">
            <div class="row d-flex">
                <div class="col-md pr-4 d-flex topper align-items-center">
                    <div class="icon mr-2 d-flex justify-content-center align-items-center">
                        <span class="icon-phone2"></span>
                    </div>
                    <span class="text">+(84).236.3667117</span>
                </div>

                <div class="col-md pr-4 d-flex topper align-items-center">
                    <div class="icon mr-2 d-flex justify-content-center align-items-center">
                        <span class="icon-paper-plane"></span>
                    </div>
                    <span class="text">info@vku.udn.vn</span>
                </div>

                <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
                    <span class="text">470 Trần Đại Nghĩa, Hòa Hải, Ngũ hành Sơn, Đà Nẵng</span>
                </div>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
<div class="container">
    <a class="navbar-brand" href="index.html">D&N</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
        aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="oi oi-menu"></span> Menu
    </button>

    <div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">

            <li class="nav-item active">
                <a href="{{ route('index') }}" class="nav-link">Trang chủ</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">Sản phẩm</a>
                <div class="dropdown-menu" aria-labelledby="dropdown04">
                    @foreach ($category as $item)
                        <a class="dropdown-item"
                            href="{{ route('sanpham', ['id' => $item['Category_ID']]) }}">
                            {{ $item['Name'] }}
                        </a>
                    @endforeach
                </div>
            </li>

            <li class="nav-item"><a href="{{ route('gioithieu') }}" class="nav-link">Giới thiệu</a></li>
            <li class="nav-item"><a href="{{ route('tintuc') }}" class="nav-link">Tin tức</a></li>
            <li class="nav-item"><a href="{{ route('lienhe') }}" class="nav-link">Liên hệ</a></li>

            {{-- GIỎ HÀNG + BADGE ĐẾM SỐ LƯỢNG --}}
            <li class="nav-item cta cta-colored">
                <a href="{{ route('giohang') }}" class="nav-link shopping-cart-icon">
                    <span class="icon-shopping_cart"></span>
                    <span id="cart-count" class="cart-badge">{{ $cartCount }}</span>
                </a>
            </li>

            {{-- USER LOGIN --}}
            @if (Auth::check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-circle-user"></i> {{ Auth::user()->Name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown04">
                        <a class="dropdown-item" href="{{ route('donmua') }}">Đơn mua</a>
                        <a class="dropdown-item" href="{{ route('logout') }}">Đăng xuất</a>
                    </div>
                </li>
            @else
                <li class="nav-item cta cta-colored">
                    <a href="{{ route('showsignin') }}" class="nav-link">
                        <i class="fa-solid fa-user"></i> Đăng nhập/Đăng kí
                    </a>
                </li>
            @endif

            {{-- SEARCH --}}
            <li class="nav-item">
                <div class="nav-search nav-link">
                    <form method="POST" action="{{ URL::to('/timkiem') }}">
                        @csrf
                        <input name="key" placeholder="Tìm kiếm" />
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </li>

        </ul>
    </div>
</div>
</nav>

<style>
/* ============================
   STYLE ICON GIỎ HÀNG + BADGE
   ============================ */
.shopping-cart-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

/* Badge số lượng */
.cart-badge {
    position: absolute;
    top: -4px;
    right: -6px;
    background: #ff3b30; /* đỏ tươi */
    color: #fff;
    font-size: 11px;
    padding: 2px 6px;
    min-width: 18px;
    min-height: 18px;
    border-radius: 50%;
    border: 2px solid #fff; /* Viền trắng đẹp */
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    line-height: 1;
}

/* Icon giỏ hàng */
.shopping-cart-icon span {
    font-size: 18px;
    color: white;
}

/* Nền xanh hover */
.nav-item.cta.cta-colored > a.nav-link {
    background: #7bb430;
    padding: 8px 14px;
    border-radius: 6px;
    transition: 0.25s ease;
}
.nav-item.cta.cta-colored > a.nav-link:hover {
    background: #6a9c2a;
}

</style>
