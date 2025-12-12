@php
    $total = $total ?? 0;
@endphp

<table class="cartdetete table" data-url="{{ route('deletecart') }}">
    <thead class="thead-primary">
        <tr class="text-center">
            <th>&nbsp;</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </thead>

    @php $total = 0; @endphp
    <tbody>
        @if (!empty($carts))
        @foreach ($carts as $id => $item)
            @php $total += $item['price'] * $item['quantity']; @endphp
            <tr class="text-center">

                <td class="image-prod">
                    <div class="img" style="background-image:url(Asset/images/{{$item['image']}});"></div>
                </td>

                <td class="product-name">
                    <h3>{{ $item['name'] }}</h3>
                </td>

                <td class="price">{{ number_format($item['price']) }}</td>

                <td class="quantity">
                    <div class="input-group">
                        <span class="input-group-text btn btn-danger" onclick="this.parentNode.querySelector('input[type=number]').stepDown()"> - </span>
                        <input type="number" value="{{ $item['quantity'] }}" class="form-control text-center" min="1" max="100">
                        <span class="input-group-text btn btn-success" onclick="this.parentNode.querySelector('input[type=number]').stepUp()"> + </span>
                    </div>
                </td>

                <td class="total">{{ number_format($item['price'] * $item['quantity']) }}</td>

                <td><a href="#" class="cartupdate" data-id="{{ $id }}"><i class="fa-solid fa-floppy-disk"></i></a></td>
                <td><a href="#" class="cartdelete" data-id="{{ $id }}"><span class="ion-ios-close"></span></a></td>

            </tr>
        @endforeach
        @else
            <tr><td colspan="7" class="text-center">Chưa có sản phẩm nào</td></tr>
        @endif
    </tbody>
</table>
