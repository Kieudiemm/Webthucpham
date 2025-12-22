@extends('index')

@section('content')
    <div class="hero-wrap hero-bread" style="background-image: url('{{ asset('Asset/images/bg_1.jpg') }}');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <p class="breadcrumbs">
                        <span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span>
                        <span>Gợi ý món ăn bằng AI</span>
                    </p>
                    <h1 class="mb-0 bread">Gợi ý nguyên liệu cho món ăn</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-md-10">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Nhập món bạn muốn nấu</h4>
                                <a href="{{ route('recipe.my_recipes') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-list mr-1"></i> Xem công thức đã hỏi
                                </a>
                            </div>
                            <form id="recipe-suggest-form" method="POST" action="{{ route('recipe.suggest.submit') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tên món ăn <span class="text-danger">(*)</span></label>
                                    <input type="text" name="dish" id="dish-input"
                                        class="form-control form-control-lg @error('dish') is-invalid @enderror"
                                        placeholder="Ví dụ: canh cà chua trứng, sườn xào chua ngọt..."
                                        value="{{ old('dish', $dish) }}">
                                    <span id="dish-error" class="invalid-feedback"
                                        style="display:none;"><strong></strong></span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold">Số người ăn <span
                                                class="text-danger">(*)</span></label>
                                        <input type="number" name="people" class="form-control" placeholder="Ví dụ: 4"
                                            min="1" value="{{ old('people', 2) }}">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold">Mức ngân sách <span
                                                class="text-danger">(*)</span></label>
                                        <select name="budget" class="form-control">
                                            <option value="thấp" {{ old('budget') == 'thấp' ? 'selected' : '' }}>Thấp
                                                (Tiết kiệm)</option>
                                            <option value="trung bình"
                                                {{ old('budget', 'trung bình') == 'trung bình' ? 'selected' : '' }}>Trung
                                                bình</option>
                                            <option value="cao" {{ old('budget') == 'cao' ? 'selected' : '' }}>Cao (Sử
                                                dụng hàng cao cấp)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 form-group mb-3">
                                        <label class="font-weight-bold">Loại món (Không bắt buộc)</label>
                                        <select name="dish_type" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Chiên">Chiên</option>
                                            <option value="Xào">Xào</option>
                                            <option value="Nướng">Nướng</option>
                                            <option value="Kho">Kho</option>
                                            <option value="Canh">Canh/Lẩu</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Yêu cầu đặc biệt (Tùy chọn)</label>
                                    <textarea name="special_request" class="form-control" rows="2"
                                        placeholder="Ví dụ: Tránh các nguyên liệu gây dị ứng như tôm, trứng, gluten... hoặc yêu cầu ít béo.">{{ old('special_request') }}</textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                        <i class="fa fa-magic mr-2"></i> Gợi ý nguyên liệu bằng AI
                                    </button>
                                </div>
                            </form>

                            <div id="recipe-suggest-loading" class="mt-3 d-none">
                                <div class="alert alert-info mb-0">Đang gọi AI... vui lòng chờ.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="recipe-suggest-results">
                @include('pages.Product.partials.recipe_suggest_results', [
                    'dish' => $dish,
                    'suggestions' => $suggestions,
                    'errorMessage' => $errorMessage,
                ])
            </div>
        </div>
    </section>

    <script>
        $(function() {
            var $form = $('#recipe-suggest-form');
            if (!$form.length) return;

            function clearDishError() {
                $('#dish-input').removeClass('is-invalid');
                $('#dish-error').hide().find('strong').text('');
            }

            function setDishError(msg) {
                $('#dish-input').addClass('is-invalid');
                $('#dish-error').show().find('strong').text(msg || 'Dữ liệu không hợp lệ.');
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                clearDishError();
                $('#recipe-suggest-loading').removeClass('d-none');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        $('#recipe-suggest-results').html((res && res.html) ? res.html : '');
                    },
                    error: function(xhr) {
                        if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                            .errors) {
                            var dishErr = xhr.responseJSON.errors.dish ? xhr.responseJSON.errors
                                .dish[0] : null;
                            if (dishErr) setDishError(dishErr);
                            return;
                        }

                        $('#recipe-suggest-results').html(
                            '<div class="row justify-content-center mb-4">' +
                            '<div class="col-md-10">' +
                            '<div class="alert alert-danger mb-0">Không thể gọi AI, vui lòng thử lại sau.</div>' +
                            '</div>' +
                            '</div>'
                        );
                    },
                    complete: function() {
                        $('#recipe-suggest-loading').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
