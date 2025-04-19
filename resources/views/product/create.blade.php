@extends('layouts.app')

@section('content')
    <div class="container">
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

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card shadow border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- First Row: Image & Price --}}
                <div class="d-flex mb-3">
                    <div class="me-3" style="flex: 1;">
                        <label for="img" class="form-label">Image Product *</label>
                        <input type="file" class="form-control" name="img" id="img" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="price" class="form-label">Price *</label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ old('price') }}" required>
                    </div>
                </div>

                {{-- Second Row: Name Product & Stock --}}
                <div class="d-flex mb-3">
                    <div class="me-3" style="flex: 1;">
                        <label for="name_product" class="form-label">Name Product *</label>
                        <input type="text" class="form-control" name="name_product" id="name_product" value="{{ old('name_product') }}" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="stock" class="form-label">Stock *</label>
                        <input type="number" class="form-control" name="stock" id="stock" value="{{ old('stock') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-save"></i> Save
                    </button>
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
