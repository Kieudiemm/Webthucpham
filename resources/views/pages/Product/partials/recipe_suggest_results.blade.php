@if ($errorMessage)
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                <i class="fa fa-exclamation-triangle mr-3 fa-2x text-warning"></i>
                <div>{{ $errorMessage }}</div>
            </div>
        </div>
    </div>
@endif

@if (!empty($suggestions))
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Thông tin món ăn --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="row no-gutters align-items-center">
                        <div class="col-md-4">
                            @if (!empty($dishImage))
                                <img src="{{ $dishImage }}" alt="{{ $dish }}" class="img-fluid" style="height: 250px; width: 100%; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fa fa-utensils fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8 p-4">
                            <span class="badge badge-success mb-2 px-3 py-2">Gợi ý từ AI</span>
                            <h2 class="font-weight-bold text-dark mb-2">{{ $dish }}</h2>
                            <div class="d-flex text-muted mb-0">
                                <div class="mr-4"><i class="fa fa-users text-success mr-2"></i><strong>Khẩu phần:</strong> {{ $people ?? 1 }} người</div>
                                <div><i class="fa fa-shopping-basket text-success mr-2"></i><strong>Nguyên liệu:</strong> {{ count($suggestions) }} loại</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bảng nguyên liệu --}}
            <div class="bg-white p-3 p-md-4 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <h4 class="mb-0 font-weight-bold">Danh sách mua sắm</h4>
                </div>

                <form method="POST" action="{{ route('recipe.add_to_cart') }}" id="recipeAddToCartForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr class="text-center text-uppercase small font-weight-bold text-muted">
                                    <th class="border-0">Sản phẩm</th>
                                    <th class="border-0 text-left">Tên nguyên liệu</th>
                                    <th class="border-0">Lượng dùng</th> {{-- Cột mới: Hiển thị nhu cầu thực tế --}}
                                    <th class="border-0">Quy cách mua</th> {{-- Cột số lượng đơn vị --}}
                                    <th class="border-0">Đơn giá</th>
                                    <th class="border-0 text-right">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suggestions as $index => $item)
                                    @php
                                        $p = $item['product'];
                                        $price = $p->Discount ?: $p->Price;
                                    @endphp
                                    <tr class="text-center">
                                        <td style="width: 80px;">
                                            <img src="{{ asset('Asset/images/' . $p->Thumbnail) }}" 
                                                 class="rounded shadow-sm" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td class="text-left">
                                            <div class="font-weight-bold text-dark">{{ $p->Title }}</div>
                                            <small class="text-muted">Kho: {{ $p->weight }} {{ $p->unit }}</small>
                                        </td>
                                        <td>
                                            {{-- Hiển thị lượng cần dùng (ví dụ: Cần dùng 300g) --}}
                                            <span class="text-primary font-weight-500">
                                                {{ $item['usage'] ?: $item['display_quantity'] }}
                                            </span>
                                        </td>
                                        <td style="width: 180px;">
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $p->product_id }}">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <input type="number" 
                                                       name="items[{{ $index }}][quantity]" 
                                                       class="form-control text-center font-weight-bold border-success" 
                                                       style="max-width: 70px; border-radius: 5px;"
                                                       value="{{ ceil($item['quantity']) }}" 
                                                       min="1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white border-left-0">{{ $item['unit'] }}</span>
                                                </div>
                                            </div>
                                            @if($item['quantity'] > 1)
                                                <small class="text-info mt-1 d-block">Làm tròn để đủ món</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ number_format($price) }}đ</span>
                                        </td>
                                        <td class="text-right text-muted small" style="max-width: 200px;">
                                            @if(!empty($item['alternative']))
                                                <div class="text-primary"><i class="fa fa-exchange-alt"></i> Thay: {{ $item['alternative'] }}</div>
                                            @endif
                                            @if(!empty($item['note']))
                                                <div>{{ $item['note'] }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-end align-items-center mt-4 border-top pt-4">
                        <div class="text-muted mb-3 mb-md-0 mr-md-4 small">
                            <i class="fa fa-info-circle mr-1"></i> Bạn có thể điều chỉnh số lượng trước khi thêm vào giỏ.
                        </div>
                        <div>
                            <button type="submit" name="action" value="cart" class="btn btn-outline-success btn-lg px-4 mr-2" style="border-radius: 10px;">
                                <i class="fa fa-cart-plus mr-2"></i> Thêm vào giỏ
                            </button>
                            <button type="submit" name="action" value="buy_now" class="btn btn-success btn-lg px-5 shadow-sm" style="border-radius: 10px;">
                                <i class="fa fa-bolt mr-2"></i> Mua ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Công thức nấu ăn --}}
            @if (!empty($cookingInstructions))
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="font-weight-bold mb-0">
                            <i class="fa fa-hat-chef text-success mr-2"></i>Hướng dẫn chế biến
                        </h4>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <hr>
                        <div class="recipe-content text-dark" style="line-height: 1.8; font-size: 1.05rem;">
                            {!! $cookingInstructions !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

<style>
    .font-weight-500 { font-weight: 500; }
    .table td, .table th { vertical-align: middle !important; }
    .recipe-content ul, .recipe-content ol { padding-left: 20px; }
    .recipe-content h3 { font-size: 1.3rem; font-weight: bold; color: #28a745; margin-top: 20px; }
</style>