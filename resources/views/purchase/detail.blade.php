@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Purchase Receipt Details</h4>
        </div>
        <div class="card-body">
            <p><strong>Transaction Number:</strong> {{ $purchase->id ?? '-' }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y, H:i') }}</p>
            <p><strong>Cashier:</strong> {{ $purchase->user->name ?? '—' }}</p>

            @if($purchase->member)
                <p><strong>Member Type:</strong> {{ $purchase->member->name ?? 'Non Member' }} (Remaining Points: {{ $purchase->member->points }})</p>
            @endif
            <hr>
            <h5>Product List</h5>
            <table class="table table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->products as $product)
                    <tr>
                        <td>{{ $product->name_product ?? '-' }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($product->pivot->quantity * $product->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>

            <div class="d-flex justify-content-end">
                <div style="min-width: 300px;">
                    <p><strong>Total:</strong> Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</p>
                    @if($purchase->used_point > 0)
                        <p><strong>Points Used ({{ $purchase->used_point }} pts):</strong> -Rp {{ number_format($purchase->used_point * 10, 0, ',', '.') }}</p>
                        <p><strong>Total After Points:</strong> Rp {{ number_format($purchase->total_price - ($purchase->used_point * 10), 0, ',', '.') }}</p>
                    @endif
                    <p><strong>Pay:</strong> Rp {{ number_format($purchase->total_pay ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Return:</strong> Rp {{ number_format($purchase->total_return ?? 0, 0, ',', '.') }}</p>
            
                    @if($purchase->member && $purchase->member->name !== 'Non Member')
                        <p><strong>Earned Points:</strong> {{ floor(($purchase->total_price - ($purchase->used_point * 10)) * 0.01) }} pts</p>
                    @endif
                </div>
            </div>
            <div class="text-center mt-4">  
                <a href="{{ route('purchase.index') }}" class="btn btn-primary">Back to Purchase List</a>
                <a href="{{ route('purchase.receipt', $purchase->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-file-earmark-arrow-down-fill"></i> Download Receipt PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
