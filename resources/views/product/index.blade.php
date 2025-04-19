@extends('layouts.app')
@section('title', 'Product - W Mart')

@section('content')
  <div class="mb-4">
    <div class="breadcrumb">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <i class="bi bi-arrow-right-short"></i> Product
        </span>
    </div>
    <h2 class="page-title">Product</h2>
  </div>

  <div class="card shadow border-0">
    <div class="card-body">
        @if(auth()->user()->isAdmin())
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('product.create') }}" class="btn btn-primary"> 
                <i class="bi bi-bag-plus"></i> Add Product
            </a>
        </div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Name Product</th>
                        <th scope="col">Price</th>
                        <th scope="col">Stock</th>
                        @if (Auth::user()->isAdmin())
                            <th scope="col">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>
                            <img src="{{ asset('storage/store/product/' . $product->img) }}" width="60" alt="Product Image">
                        </td>
                        <td>{{ $product->name_product }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        @if (Auth::user()->isAdmin())
                        <td>
                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning"> 
                                <i class="bi bi-pencil-fill"></i> Edit
                            </a>
                            <button 
                                type="button" 
                                class="btn btn-success"
                                data-bs-toggle="modal" 
                                data-bs-target="#updateStockModal"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name_product }}"
                                data-stock="{{ $product->stock }}"
                            >
                                <i class="bi bi-arrow-repeat"></i> Update
                            </button>
                            <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-danger"> 
                                    <i class="bi bi-trash3-fill"></i> Delete
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No product available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Modal -->
            <div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <form method="POST" id="update-stock-form">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal-name-product" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="modal-name-product" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="modal-stock" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"> <i class="bi bi-save"></i> Save Stock</button>
                    </div>
                    </div>
                </form>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                {{ $products->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
            </div>            
        </div>
      </div>
  </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('updateStockModal');
  modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const stock = button.getAttribute('data-stock');

    modal.querySelector('#modal-name-product').value = name;
    modal.querySelector('#modal-stock').value = stock;
    
    const form = modal.querySelector('#update-stock-form');
    form.action = `/product/${id}`;
  });
});
</script>
@endsection

