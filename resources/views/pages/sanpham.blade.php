@extends('index')
@section('content')
<div class="hero-wrap hero-bread" style="background-image: url('Asset/images/bg_1.jpg');">
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center">
        <div class="col-md-9 ftco-animate text-center">
            <p class="breadcrumbs"><span class="mr-2"><a href="">Trang chủ</a></span> <span>Sản phẩm</span></p>
          <h1 class="mb-0 bread">{{ $namecategory->Name ?? 'Danh mục' }}</h1>
        </div>
      </div>
    </div>
</div>

<section class="ftco-section">
  <div class="container">

    <div class="row justify-content-center mb-5 pb-3">
        <div class="col-md-7 heading-section ftco-animate text-center">
            <span class="subheading" style="text-transform: uppercase;">
                SẢN PHẨM CỦA DANH MỤC {{ $namecategory->Name ?? 'Danh mục' }}
            </span>
        </div>
    </div>

    <div class="row" style="margin-left:900px">
        <div class="col-md-12">
          <label for="amount">Sắp xếp theo</label>
          <form action="{{ route('loc') }}" method="POST">
            @csrf
            <select name="sort" id="sort" class="form-control">
              <option value="none">--Lọc--</option>
              <option value="tang_dan">--Giá tăng dần--</option>
              <option value="giam_dan">--Giá giảm dần--</option>
              <option value="kytu_az">--A đến Z--</option>
              <option value="kytu_za">--Z đến A--</option>
            </select>
            <button class="btnRegister" type="submit"
                style="background: #82ae46; margin-top:15px; margin-bottom:15px">Lọc</button>
          </form>
        </div>
    </div>

    <div class="row">
      @foreach ($sanpham as $item)
        <div class="col-md-6 col-lg-3 ftco-animate">
            <div class="product">

                {{-- ================= IMAGE ================= --}}
                <a href="{{ route('chitietsanpham', ['id' => $item->product_id]) }}"
                   class="img-prod">
                    <img id="Product_img_{{$item->product_id}}"
                         class="img-fluid"
                         src="Asset/images/{{$item->Thumbnail}}"
                         alt="{{$item->Title}}">
                    <div class="overlay"></div>
                </a>

                <div class="text py-3 pb-4 px-3 text-center">

                    {{-- ================= TITLE ================= --}}
                    <h3 id="Product_title_{{$item->product_id}}"
                        title="{{$item->Title}}">
                        <a href="{{ route('chitietsanpham', ['id'=> $item->product_id]) }}">
                            {{$item->Title}}
                        </a>
                    </h3>

                    <div class="d-flex">
                        <div class="pricing">
                            {{-- ================= PRICE ================= --}}
                            <p class="price">
                                <span id="Product_price_{{$item->product_id}}"
                                      title="{{$item->Discount}}">
                                      {{number_format($item->Discount,0,',','.')}}đ
                                </span>
                            </p>
                            <p class="mt-2" style="font-size: 14px; color: #555;">
                                <strong>Size:</strong> {{ $item->weight ?? 'Không có' }} kg
                            </p>
                        </div>
                    </div>

                    <div class="bottom-area d-flex px-3">
                        <div class="m-auto d-flex">

                            <a href="{{route('chitietsanpham', ['id'=> $item->product_id])}}"
                               class="add-to-cart d-flex justify-content-center align-items-center text-center">
                                <span><i class="ion-ios-menu"></i></span>
                            </a>

                            <a href="#"
                               id="addtocart"
                               class="addtocart d-flex justify-content-center align-items-center mx-1"
                               data-url="{{ route('addtocart', ['id'=>$item->product_id]) }}"
                               onclick="addtocart">
                                <span><i class="ion-ios-cart"></i></span>
                            </a>

                            {{-- ================= HEART (YÊU THÍCH) ================= --}}
                            <a class="heart d-flex justify-content-center align-items-center"
                                id="{{$item->product_id}}"
                                onclick="handleWishlistClick(this)">
                                    <span><i class="ion-ios-heart"></i></span>
                            </a>


                        </div>
                    </div>

                </div>
            </div>
        </div>
      @endforeach
    </div>

  </div>
</section>

<section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
  <div class="container py-4">
    <div class="row d-flex justify-content-center py-5">
      <div class="col-md-6">
          <h2 style="font-size: 22px;" class="mb-0">Đăng kí nhận tin khuyến mãi</h2>
          <span>Nhận thông tin cập nhật qua email về cửa hàng mới nhất và các ưu đãi đặc biệt</span>
      </div>
      <div class="col-md-6 d-flex align-items-center">
        <form action="#" class="subscribe-form">
          <div class="form-group d-flex">
            <input type="text" class="form-control" placeholder="Email của bạn..">
            <input type="submit" value="Subscribe" class="submit px-3">
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
