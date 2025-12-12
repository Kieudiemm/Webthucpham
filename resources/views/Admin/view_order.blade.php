@extends('index_Admin')
@section('Admin_Content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Thông tin khách hàng
    </div>

    <div class="table-responsive">
      <table  class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>Tên khách hàng</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $customer->Name }}</td>
            <td>{{ $customer->email }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<br><br>

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Thông tin vận chuyển
    </div>

    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>Họ và tên</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Email</th>
            <th>Ghi chú</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $customer->FullName }}</td>
            <td>{{ $customer->Address }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->note }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<br><br>

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Liệt kê chi tiết đơn hàng
    </div>

    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng</th>
            <th>Giá</th>
            <th>Tổng</th>
          </tr>
        </thead>

        <tbody>

        @php
            $i = 0;
            $total = 0;
        @endphp

        @foreach($order_details as $detail)
            @php
                $i++;
                $subtotal = $detail->price * $detail->quantity;
                $total += $subtotal;
            @endphp

            <tr>
                <td>{{ $i }}</td>
                <td>{{ $detail->Name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->price, 0, ',', '.') }}đ</td>
                <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
            </tr>
        @endforeach

        @php
            $total1 = $total + $order->ship - $order->coupon;
        @endphp

        <tr>
            <td>Phí vận chuyển:</td>
            <td colspan="3"></td>
            <td>{{ number_format($order->ship, 0, ',', '.') }}đ</td>
        </tr>

        <tr>
            <td>Giảm giá:</td>
            <td colspan="3"></td>
            <td>{{ number_format($order->coupon, 0, ',', '.') }}đ</td>
        </tr>

        <tr>
            <td>Thanh toán:</td>
            <td colspan="3"></td>
            <td>{{ number_format($total1, 0, ',', '.') }}đ</td>
        </tr>

        </tbody>
      </table>
    </div>

  </div>
</div>

@endsection
