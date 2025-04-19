@extends('layouts.app')
@section('title', 'Purchase - W Mart')

@section('content')
  <div class="mb-4">
    <div class="breadcrumb">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <i class="bi bi-arrow-right-short"></i> Purchase
        </span>
    </div>
    <h2 class="page-title">Purchase</h2>
  </div>
  <div class="card shadow border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <div class="d-flex justify-content-end mb-3">
              <a href="{{ route('purchase.export') }}" class="btn btn-success">
                <i class="bi bi-download"></i> Download Excel
              </a>
              @if(Auth::check() && Auth::user()->role === 'staff')
                <a href="{{ route('purchase.create') }}" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Purchase
                </a>
              @endif
            </div>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Customer Name</th>
              <th scope="col">Date</th>
              <th scope="col">Total Amount</th>
              <th scope="col">Made By</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($purchases as $purchase)
              <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $purchase->member->name ?? 'Non Member' }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y, H:i A') }}</td>
                <td>Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                <td>{{ $purchase->user->name ?? '-' }}</td>
                <td>
                  <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#purchaseModal{{ $purchase->id }}">
                    <i class="bi bi-eye"></i> Show
                  </button>                  
                  {{-- <a href="{{ route('purchase.download', $purchase->id) }}" class="btn btn-primary"">
                    <i class="bi bi-download"></i> Download Receipt
                  </a> --}}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">No Purchase Data</td>
              </tr>
            @endforelse
            @foreach ($purchases as $purchase)
              <div class="modal fade" id="purchaseModal{{ $purchase->id }}" tabindex="-1" aria-labelledby="purchaseModalLabel{{ $purchase->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="purchaseModalLabel{{ $purchase->id }}">Purchase Detail</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Customer:</strong> {{ $purchase->member->name ?? 'Non Member' }}</p>
                      <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y, H:i A') }}</p>    
                      <p><strong>Total Price:</strong> Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</p>
                      <p><strong>Total Pay:</strong> Rp {{ number_format($purchase->total_pay, 0, ',', '.') }}</p>
                      <p><strong>Return:</strong> Rp {{ number_format($purchase->total_return, 0, ',', '.') }}</p>
                      <p><strong>Made By:</strong> {{ $purchase->user->name ?? '-' }}</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </tbody>
        </table>
        <div class="d-flex justify-content-end">
          {{ $purchases->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
@endsection
