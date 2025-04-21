@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Purchase Confirmation</h4>
    
    <div class="card shadow-sm border rounded-4 p-4">
        <div class="mb-4">
            @php $totalFinal = 0; @endphp
            @foreach ($products as $product)
                @php
                    $qty = $quantities[$product->id] ?? 0;
                    $subtotal = $qty * $product->price;
                    $totalFinal += $subtotal;
                @endphp
                @if ($qty > 0)
                    <div class="d-flex justify-content-between">
                        <div>{{ $product->name_product }}</div>
                        <div>Rp {{ number_format($product->price, 0, ',', '.') }} x {{ $qty }} = Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                @endif
            @endforeach
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <div>Total</div>
                <div>Rp {{ number_format($totalFinal, 0, ',', '.') }}</div>
            </div>
        </div>

        <form id="purchase-form" action="{{ route('purchase.store') }}" method="POST">
            @csrf

            @foreach ($products as $product)
                @php $qty = $quantities[$product->id] ?? 0; @endphp
                @if ($qty > 0)
                    <input type="hidden" name="products[{{ $product->id }}][id]" value="{{ $product->id }}">
                    <input type="hidden" name="products[{{ $product->id }}][quantity]" value="{{ $qty }}">
                @endif
            @endforeach

            <input type="hidden" name="total" value="{{ $totalFinal }}">

            <div class="mb-3">
                <label class="form-label">Status Member</label>
                <select name="member_type" class="form-select" required>
                    <option value="non_member" selected>Non-Member</option>
                    <option value="member">Member</option>
                </select>            
            </div>

            <div id="member-form" style="display: none;">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control" placeholder="08xxxxx" id="phone_number">
            </div>

            <div class="mt-3">
                <label for="payment_amount" class="form-label">Amount Paid</label>
                <input type="text" name="payment_amount" class="form-control" id="payment_amount" required>
            </div>

            <button type="submit" class="btn btn-success mt-3 w-100">Pay</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentInput = document.getElementById('payment_amount');
        const memberStatus = document.querySelector('select[name="member_type"]');
        const memberForm = document.getElementById('member-form');
        const total = {{ $totalFinal }};

        paymentInput.addEventListener('input', function (e) {
            let input = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = 'Rp ' + input.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });

        memberForm.style.display = memberStatus.value === 'member' ? 'block' : 'none';
        memberStatus.addEventListener('change', function () {
            memberForm.style.display = this.value === 'member' ? 'block' : 'none';
        });

        document.getElementById('purchase-form').addEventListener('submit', function (e) {
            const cleanInput = paymentInput.value.replace(/[^0-9]/g, '');
            const paymentAmount = parseInt(cleanInput);

            if (paymentAmount < total) {
                e.preventDefault();
                alert('Insufficient payment amount.');
            }
        });

        const phoneInput = document.getElementById('phone_number');

        function toggleMemberForm() {
            const isMember = memberStatus.value === 'member';
            memberForm.style.display = isMember ? 'block' : 'none';
            if (isMember) {
                phoneInput.setAttribute('required', 'required');
            } else {
                phoneInput.removeAttribute('required');
            }
        }

        toggleMemberForm();
        memberStatus.addEventListener('change', toggleMemberForm);
    });
</script>
@endsection
