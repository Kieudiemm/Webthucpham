@extends('index')
@section('content')
<div class="hero-wrap hero-bread " style="background-image: url('Asset/images/bg_1.jpg');">
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center">
        <div class="col-md-9 ftco-animate text-center">
            <p class="breadcrumbs"><span class="mr-2"><a href="index.html">Trang chủ</a></span> <span>Giỏ hàng</span></p>
          <h1 class="mb-0 bread">Giỏ hàng của tôi</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="ftco-section ftco-cart " >
          <div class="container">
              <div class="row">
              <div class="col-md-12 ftco-animate">
                <div class="cart_component">
                  <div class="cart-list "  >
                      <table class=" cartdetete table" data-url="{{ route('deletecart')}}">
                          <thead class="thead-primary">
                            <tr class="text-center">

                              <th>&nbsp;</th>
                              <th>Tên sản phẩm</th>
                              <th>Giá</th>
                              <th>Số lượng</th>
                              <th>Tổng tiền</th>
                              <th>&nbsp;</th>
                              <th>&nbsp;</th>
                            </tr>
                          </thead>
                          @php
                              $total =0;
                          @endphp
                          <tbody>
                            @if (Session::has("cart")!=null)
                            @foreach ($carts as $id => $item )
                            @php
                                $total += $item['price']* $item['quantity'];
                            @endphp
                            <tr class="text-center ">


                              <td class="image-prod"><div class="img" style="background-image:url(Asset/images/{{$item['image']}});"></div></td>

                              <td class="product-name">
                                  <h3>{{$item['name']}}</h3>


                              <td class="price">{{number_format($item['price'])}}</td>

                              <td class="quantity">
                                <div class="input-group">
                                    <span class="input-group-text btn btn-danger" onclick="this.parentNode.querySelector('input[type=number]').stepDown()"> -     </span>
                                    <input type="number" value="{{$item['quantity']}}" class="form-control text-center" min="1" max="100">
                                    <span class="input-group-text btn btn-success" onclick="this.parentNode.querySelector('input[type=number]').stepUp()"> +    </span>

                                </div>
                            </td>

                              <td class="total">{{number_format($item['price']*$item['quantity'])}}</td>
                              <td class="product-remove "   ><a href="#" class="cartupdate" data-id="{{$id}}"><i class="fa-solid fa-floppy-disk"></i></a></td>
                              <td class="product-remove "   ><a href="#" class="cartdelete" data-id="{{$id}}"><span class="ion-ios-close"></span></a></td>

                            </tr><!-- END TR-->


                            @endforeach
                            @endif
                            @if (Session::has("cart")==null)
                                <p>Chưa có sản phẩm nào</p>
                            @endif


                          </tbody>
                        </table>
                    </div>
                </div>
              </div>
          </div>
          <div class="row align-items-end " style="margin-left:450px;">
<form action="{{ route('giohang') }}" method="post" style="width: 700px;">
    @csrf
    <div class="col-md-10">
        <div class="form-group">
            <label>Chọn tỉnh/thành</label>
            <select name="city" id="city" class="form-control">
                <option value="">-- Chọn tỉnh/thành --</option>
                @foreach ($locations as $c)
                    <option value="{{ $c['code'] }}">{{ $c['name'] }}</option>
                @endforeach
            </select>

            @error('city')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-group">
            <label>Chọn quận/huyện</label>
            <select name="province" id="province" class="form-control">
                <option value="">-- Chọn quận/huyện --</option>
            </select>

            @error('province')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-10" style="border-bottom: 2px solid #e1e1e1">
        <div class="form-group">
            <label>Chọn xã/phường</label>
            <select name="wards" id="wards" class="form-control">
                <option value="">-- Chọn xã/phường --</option>
            </select>

            @error('wards')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>


                  <div class="col-md-10" style="border-bottom: 2px solid #e1e1e1">
                  <div class="form-group">
                      <label for="phone">Nhập mã giảm giá</label>
                    <input name="coupon" type="text" class="form-control coupon" placeholder="Nhập mã giảm giá của bạn" >
                  </div>
                </div>

              <div class="col-lg-10 mt-5 cart-wrap ftco-animate">
                  <div class="cart-total mb-3">

                      <p class="d-flex total-price">
                          <span>Tổng tiền giỏ hàng</span>
                          <span></span>
                          <span>{{number_format($total)}}</span>

                      </p>
                  </div>


              </div>
              <button class="btn btn-primary py-3 px-4 " style="margin-left:240px;" type="submit" name="" >Thanh toán</button>
            </form>

        </div>
          </div>
          </div>
      </section>

      <section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
    <div class="container py-4">
      <div class="row d-flex justify-content-center py-5">
        <div class="col-md-6">
            <h2 style="font-size: 22px;" class="mb-0">Theo dõi bản tin của chúng tôi</h2>
            <span>Nhận thông tin cập nhật qua email về các cửa hàng mới nhất của chúng tôi và các ưu đãi đặc biệt</span>
        </div>
        <div class="col-md-6 d-flex align-items-center">
          <form action="#" class="subscribe-form">
            <div class="form-group d-flex">
              <input type="text" class="form-control" placeholder="Nhập địa chỉ email">
              <input type="submit" value="Subscribe" class="submit px-3">
            </div>
          </form>

      </div>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Danh sách tỉnh → huyện → xã (lấy từ Controller)
let locations = @json($locations);

// Khi chọn tỉnh
$('#city').on('change', function () {
    let cityCode = $(this).val();

    // Reset dropdown
    $('#province').html('<option value="">-- Chọn quận/huyện --</option>');
    $('#wards').html('<option value="">-- Chọn xã/phường --</option>');

    if (!cityCode) return;

    // Tìm tỉnh
    let city = locations.find(item => item.code == cityCode);
    if (!city || !city.districts) return;

    city.districts.forEach(d => {
        $('#province').append(`<option value="${d.code}">${d.name}</option>`);
    });
});


// Khi chọn huyện
$('#province').on('change', function () {
    let districtCode = $(this).val();
    let cityCode = $('#city').val();

    $('#wards').html('<option value="">-- Chọn xã/phường --</option>');

    if (!cityCode || !districtCode) return;

    // Tìm tỉnh
    let city = locations.find(item => item.code == cityCode);
    if (!city || !city.districts) return;

    // Tìm huyện theo code
    let district = city.districts.find(d => d.code == districtCode);
    if (!district || !district.wards) return;

    district.wards.forEach(w => {
        $('#wards').append(`<option value="${w.code}">${w.name}</option>`);
    });
});

$(document).on('click', '.cartdelete', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let url = $('.cartdetete').data('url');

    $.ajax({
        type: 'GET',
        url: url,
        data: { id: id },
        success: function (response) {
            if (response.code == 200) {

                // Cập nhật lại table
                $('.cartdetete').parent().html(response.html);

                // Cập nhật số lượng icon giỏ hàng
                $('#cart-count').text(response.qty);

                // Cập nhật tổng tiền
                $('.total-price span:last-child').text(response.total);
            }
        }
    });
});



</script>


@endsection



