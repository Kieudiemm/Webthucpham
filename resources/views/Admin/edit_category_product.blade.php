@extends('index_Admin')
@section('Admin_Content')
<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">
            Cập nhập danh mục sản phẩm
        </header>

        <div class="panel-body">

            <div class="position-center">
                <form role="form" action="{{ URL::to('/update-category-product/'.$edit_category_product->Category_ID) }}" method="post">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="exampleInputEmail1">Tên danh mục</label>
                        <input type="text" value="{{ $edit_category_product->Name }}"
                               name="category_product_name" class="form-control" placeholder="Tên danh mục">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">Mô tả danh mục</label>
                        <textarea rows="5" class="form-control" name="category_product_desc" placeholder="Mô tả danh mục">
                            {{ $edit_category_product->desc }}
                        </textarea>
                    </div>

                    <button type="submit" class="btn btn-info">Cập nhập</button>
                </form>
            </div>

        </div>
    </section>
</div>
@endsection
