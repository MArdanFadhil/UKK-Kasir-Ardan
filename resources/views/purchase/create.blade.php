@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Create New Purchase</h3>
    <form action="{{ route('purchase.selectProduct') }}" method="POST">
        @csrf
        <div class="row">
            @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('storage/store/product/' . $product->img) }}" class="card-img-top" alt="{{ $product->name_product }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name_product }}</h5>
                        <p>Price: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p>Stock: {{ $product->stock }}</p>

                        @if($product->stock > 0)
                            <div class="input-group mb-3">
                                <button class="btn btn-outline-secondary btn-decrement" type="button">-</button>
                                <input type="number" name="quantities[{{ $product->id }}]" class="form-control text-center quantity-input" value="0" min="0" max="{{ $product->stock }}">
                                <button class="btn btn-outline-secondary btn-increment" type="button">+</button>
                            </div>
                            <p>Subtotal: Rp <span class="subtotal" data-price="{{ $product->price }}">0</span></p>
                        @else
                            <div class="alert alert-danger">Stock not available</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <input type="hidden" name="total_amount" id="total-amount-input" value="0">

        <div class="fixed-bottom bg-white py-3 px-4 shadow-sm border-top" style="margin-left: 250px; z-index: 1030;">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <h5 class="mb-2">Total: Rp <span id="total-amount">0</span></h5>
                <div id="selected-products" class="mb-2"></div>
                <button type="submit" class="btn btn-primary px-5">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const subtotals = document.querySelectorAll('.subtotal');
    const totalAmountDisplay = document.getElementById('total-amount');
    const totalAmountInput = document.getElementById('total-amount-input');
    const submitButton = document.querySelector('button[type="submit"]');

    function updateTotals() {
        let total = 0;
        let productSelected = false;
        quantityInputs.forEach((input, index) => {
            const qty = parseInt(input.value) || 0;
            const price = parseInt(subtotals[index].dataset.price);
            const subtotal = qty * price;
            subtotals[index].innerText = subtotal.toLocaleString('id-ID');
            total += subtotal;

            // Check if any product is selected (quantity > 0)
            if (qty > 0) {
                productSelected = true;
            }
        });

        totalAmountDisplay.innerText = total.toLocaleString('id-ID');
        totalAmountInput.value = total;

        // Enable or disable submit button based on whether a product is selected
        if (productSelected) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    document.querySelectorAll('.btn-increment').forEach((btn, index) => {
        btn.addEventListener('click', function () {
            const input = quantityInputs[index];
            const max = parseInt(input.getAttribute('max'));
            let value = parseInt(input.value) || 0;
            if (value < max) {
                input.value = value + 1;
                updateTotals();
            }
        });
    });

    document.querySelectorAll('.btn-decrement').forEach((btn, index) => {
        btn.addEventListener('click', function () {
            const input = quantityInputs[index];
            let value = parseInt(input.value) || 0;
            if (value > 0) {
                input.value = value - 1;
                updateTotals();
            }
        });
    });

    quantityInputs.forEach((input) => {
        input.addEventListener('input', updateTotals);
    });
    updateTotals();
});
</script>