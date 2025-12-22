@extends('index')

@section('content')
    <div class="hero-wrap hero-bread" style="background-image: url('{{ asset('Asset/images/bg_1.jpg') }}');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <p class="breadcrumbs">
                        <span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span>
                        <span>Công thức của tôi</span>
                    </p>
                    <h1 class="mb-0 bread">Danh sách công thức đã hỏi</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12 text-center mb-3">
                    <a href="{{ route('recipe.suggest.form') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-plus mr-2"></i> Tạo công thức mới
                    </a>
                </div>
            </div>

            {{-- Bộ lọc và tìm kiếm --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="GET" action="{{ route('recipe.my_recipes') }}" class="row align-items-end">
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold">Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Tên món ăn..." value="{{ $search }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="font-weight-bold">Ngân sách</label>
                                    <select name="budget" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="thấp" {{ $selectedBudget == 'thấp' ? 'selected' : '' }}>Thấp</option>
                                        <option value="trung bình" {{ $selectedBudget == 'trung bình' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="cao" {{ $selectedBudget == 'cao' ? 'selected' : '' }}>Cao</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="font-weight-bold">Loại món</label>
                                    <select name="dish_type" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="Chiên" {{ $selectedDishType == 'Chiên' ? 'selected' : '' }}>Chiên</option>
                                        <option value="Xào" {{ $selectedDishType == 'Xào' ? 'selected' : '' }}>Xào</option>
                                        <option value="Nướng" {{ $selectedDishType == 'Nướng' ? 'selected' : '' }}>Nướng</option>
                                        <option value="Kho" {{ $selectedDishType == 'Kho' ? 'selected' : '' }}>Kho</option>
                                        <option value="Canh" {{ $selectedDishType == 'Canh' ? 'selected' : '' }}>Canh/Lẩu</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-search mr-1"></i> Lọc
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($recipes->count() > 0)
                <div class="row">
                    @foreach($recipes as $recipe)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-sm h-100 recipe-card" style="cursor: pointer;" 
                                 data-recipe-id="{{ (string)$recipe->_id }}">
                                @if($recipe->dish_image)
                                    <img src="{{ $recipe->dish_image }}" class="card-img-top" 
                                         alt="{{ $recipe->dish_name }}" 
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fa fa-utensils fa-4x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $recipe->dish_name }}</h5>
                                    <p class="card-text text-muted mb-2">
                                        <i class="fa fa-users mr-1"></i> {{ $recipe->people_count }} người
                                        <span class="mx-2">|</span>
                                        <i class="fa fa-money-bill mr-1"></i> {{ ucfirst($recipe->budget) }}
                                    </p>
                                    @if($recipe->dish_type)
                                        <p class="card-text">
                                            <span class="badge badge-info">{{ $recipe->dish_type }}</span>
                                        </p>
                                    @endif
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fa fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($recipe->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-sm btn-outline-primary btn-block view-recipe-btn" 
                                            data-recipe-id="{{ (string)$recipe->_id }}">
                                        <i class="fa fa-eye mr-1"></i> Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Phân trang --}}
                @if($recipes->hasPages())
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center">
                                {{ $recipes->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle fa-3x mb-3"></i>
                            <h4>Bạn chưa có công thức nào</h4>
                            <p class="mb-0">Hãy tạo công thức mới để bắt đầu!</p>
                            <a href="{{ route('recipe.suggest.form') }}" class="btn btn-primary mt-3">
                                <i class="fa fa-plus mr-2"></i> Tạo công thức mới
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Popup Modal Chi tiết công thức -->
    <div class="modal fade" id="recipeDetailModal" tabindex="-1" role="dialog" aria-labelledby="recipeDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recipeDetailModalLabel">Chi tiết công thức</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="recipeDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Xử lý click vào card hoặc button xem chi tiết
            $('.recipe-card, .view-recipe-btn').on('click', function(e) {
                e.preventDefault();
                var recipeId = $(this).data('recipe-id');
                
                if (!recipeId) {
                    recipeId = $(this).closest('.recipe-card').data('recipe-id');
                }

                if (recipeId) {
                    loadRecipeDetail(recipeId);
                }
            });

            function loadRecipeDetail(recipeId) {
                // Hiển thị modal và loading
                $('#recipeDetailModal').modal('show');
                $('#recipeDetailContent').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải chi tiết công thức...</p>
                    </div>
                `);

                // Gọi API để lấy chi tiết
                $.ajax({
                    url: '/cong-thuc/' + recipeId,
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.recipe) {
                            displayRecipeDetail(response.recipe);
                        } else {
                            $('#recipeDetailContent').html(`
                                <div class="alert alert-danger">
                                    Không tìm thấy công thức này.
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        $('#recipeDetailContent').html(`
                            <div class="alert alert-danger">
                                Có lỗi xảy ra khi tải chi tiết công thức. Vui lòng thử lại sau.
                            </div>
                        `);
                    }
                });
            }

            function displayRecipeDetail(recipe) {
                var ingredientsHtml = '';
                if (recipe.ingredients_json && Array.isArray(recipe.ingredients_json)) {
                    ingredientsHtml = '<ul class="list-group mb-3">';
                    recipe.ingredients_json.forEach(function(ing) {
                        var usageDisplay = ing.usage_str || (ing.quantity + ' ' + (ing.unit || ''));
                        ingredientsHtml += `
                            <li class="list-group-item">
                                <strong>${ing.title || ''}</strong>
                                <span class="badge badge-info ml-2">Cần dùng: ${usageDisplay}</span>
                                ${ing.note ? '<br><small class="text-muted"><i class="fa fa-info-circle"></i> ' + ing.note + '</small>' : ''}
                                ${ing.alternative ? '<br><small class="text-primary"><i class="fa fa-exchange-alt"></i> Thay thế: ' + ing.alternative + '</small>' : ''}
                            </li>
                        `;
                    });
                    ingredientsHtml += '</ul>';
                }

                var html = `
                    <div class="recipe-detail">
                        <div class="row mb-3">
                            <div class="col-md-4 text-center">
                                ${recipe.dish_image ? 
                                    `<img src="${recipe.dish_image}" class="img-fluid rounded" alt="${recipe.dish_name}" style="max-height: 250px; object-fit: cover;">` :
                                    `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 250px;">
                                        <i class="fa fa-utensils fa-4x text-muted"></i>
                                    </div>`
                                }
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-2">${recipe.dish_name || ''}</h3>
                                <p class="mb-2">
                                    <i class="fa fa-users mr-2"></i> <strong>Số người:</strong> ${recipe.people_count || ''} người
                                </p>
                                <p class="mb-2">
                                    <i class="fa fa-money-bill mr-2"></i> <strong>Ngân sách:</strong> ${recipe.budget ? recipe.budget.charAt(0).toUpperCase() + recipe.budget.slice(1) : ''}
                                </p>
                                ${recipe.dish_type ? `<p class="mb-2"><span class="badge badge-info">${recipe.dish_type}</span></p>` : ''}
                                ${recipe.special_request ? `<p class="mb-2"><strong>Yêu cầu đặc biệt:</strong> ${recipe.special_request}</p>` : ''}
                                <p class="text-muted">
                                    <small><i class="fa fa-calendar mr-1"></i> ${recipe.created_at ? new Date(recipe.created_at).toLocaleString('vi-VN') : ''}</small>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5><i class="fa fa-list mr-2"></i>Danh sách nguyên liệu</h5>
                            ${ingredientsHtml || '<p class="text-muted">Không có thông tin nguyên liệu.</p>'}
                        </div>

                        <div class="mb-3">
                            <h5><i class="fa fa-book mr-2"></i>Công thức nấu ăn</h5>
                            <div class="recipe-content p-3 bg-light rounded" style="line-height: 1.8;">
                                ${recipe.cooking_instructions ? recipe.cooking_instructions : 'Chưa có công thức chi tiết.'}
                            </div>
                        </div>
                    </div>
                `;

                $('#recipeDetailContent').html(html);
            }
        });
    </script>

    <style>
        .recipe-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .recipe-content {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection

