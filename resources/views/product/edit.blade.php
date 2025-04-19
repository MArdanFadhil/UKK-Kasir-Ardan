@extends('layouts.app')
@section('title', 'Edit Product - W Mart')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-0 rounded-3">
        <div class="card-body">
            <div class="breadcrumb mb-2">
                <span class="breadcrumb-item">
                    <i class="bi bi-house-door"></i>
                    <i class="bi bi-arrow-right-short"></i> Product
                </span>
            </div>
            <h2 class="page-title m-0">Product</h2>
        </div>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="card shadow border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="mb-3">
                    <label for="img" class="form-label">Product Image</label><br>
                    @if ($product->img)
                        <img src="{{ asset('storage/store/product/' . $product->img) }}" width="100" class="mb-2 rounded shadow-sm d-block">
                    @endif
                    <input type="file" class="form-control" name="img" id="img">
                </div>

                <div class="mb-3">
                    <label for="name_product" class="form-label">Product Name</label>
                    <input type="text" name="name_product" id="name_product" class="form-control"
                        value="{{ old('name_product', $product->name_product) }}" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" class="form-control"
                        value="{{ old('price', (int) $product->price) }}">
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" id="stock" class="form-control"
                        value="{{ old('stock', $product->stock) }}" disabled>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
