@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border rounded-4 p-4">
        <h4 class="mb-4">Member Payment</h4>
        
        <p><strong>Available Points:</strong> <span id="available-points">{{ $member->points ?? 0 }}</span> poin</p>
        <hr>

        @foreach ($products as $product)
            @php
                $qty = $quantities[$product->id] ?? 0;
                $subtotal = $qty * $product->price;
            @endphp

            @continue($qty <= 0)

            <div class="d-flex justify-content-between">
                <div>{{ $product->name }}</div>
                <div>Rp {{ number_format($product->price, 0, ',', '.') }} x {{ $qty }} = Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <div>Total</div>
            <div>Rp {{ number_format($total, 0, ',', '.') }}</div>
        </div>
        <div class="d-flex justify-content-between fw-bold">
            <div>Total After Using Points</div>
            <div id="total-after-point">Rp {{ number_format($total, 0, ',', '.') }}</div>
        </div>

        <form action="{{ route('purchase.storeMember') }}" method="POST" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="member_name" class="form-label">Member Name</label>
                <input type="text" name="member_name" id="member_name" class="form-control" value="{{ $member->name }}" required>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $member->phone_number ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="used_point" class="form-label">Use Points</label>
                <input type="number" name="used_point" id="used_point" class="form-control" max="{{ $member->points }}" value="0"{{ session('is_new_member') ? 'disabled' : '' }}>
            </div>

            <div class="mb-3">
                <label for="payment_amount" class="form-label">Amount Paid (Cash)</label>
                <input type="text" name="payment_amount" id="payment_amount" class="form-control" placeholder="Rp 0">
            </div>

            <button type="submit" class="btn btn-primary">Pay Now</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone_number');
        const nameInput = document.getElementById('member_name');
        const usedPointInput = document.getElementById('used_point');
        const availablePointsSpan = document.getElementById('available-points');
        const pointsValue = 10; // Setiap poin bernilai 10 IDR
        const totalPrice = {{ $total }};
        const totalAfterPointDiv = document.getElementById('total-after-point');

        function updateTotalAfterPoint() {
            let usedPoints = parseInt(usedPointInput.value) || 0;
            let totalAfterPoint = totalPrice - (usedPoints * pointsValue);

            if (totalAfterPoint < 0) {
                totalAfterPoint = 0;
                usedPointInput.value = Math.floor(totalPrice / pointsValue);
            }

            totalAfterPointDiv.innerText = 'Rp ' + totalAfterPoint.toLocaleString('id-ID');
        }

        phoneInput.addEventListener('blur', function () {
            const phone = phoneInput.value.trim();
            console.log('Checking phone number:', phone);

            if (phone.length >= 8) {
                nameInput.placeholder = 'Checking...';
                
                fetch(`/member/check?phone=${encodeURIComponent(phone)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        
                        if (data.exists) {
                            nameInput.value = data.name;
                            availablePointsSpan.innerText = data.points;
                            usedPointInput.max = data.points;
                            console.log('Member found:', data.name);
                        } else {
                            nameInput.value = '';
                            availablePointsSpan.innerText = '0';
                            usedPointInput.max = 0;
                            usedPointInput.value = 0;
                            console.log('No member found with that phone number');
                        }
                        updateTotalAfterPoint();
                    })
                    .catch(error => {
                        console.error('Error checking member:', error);
                        nameInput.value = '';
                    });
            }
        });

        usedPointInput.addEventListener('input', updateTotalAfterPoint);
        updateTotalAfterPoint();
    });
</script>
@endsection