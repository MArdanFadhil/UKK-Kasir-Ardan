<!DOCTYPE html>
<html>
<head>
    <title>Purchasing Structure</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 5px; border: 1px solid #000; text-align: left; }
    </style>
</head>
<body>
    <h2>Purchasing Structure</h2>
    <p><strong>Date:</strong> {{ optional($purchase->created_at)->format('d M Y H:i') }}</p>
    <p><strong>Staff:</strong> {{ $purchase->user->name }}</p>
    <p><strong>Member:</strong> {{ $purchase->member->name }} ({{ $purchase->member->phone_number }})</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->products as $product)
                <tr>
                    <td>{{ $product->name_product }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p><strong>Total Price:</strong> Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</p>
    <p><strong>Points Used:</strong> {{ $purchase->used_point }}</p>
    <p><strong>Total After Points:</strong> 
        Rp {{ number_format($purchase->total_price - ($purchase->used_point * 10), 0, ',', '.') }}
    </p>
    <p><strong>Paid:</strong> Rp {{ number_format($purchase->total_pay, 0, ',', '.') }}</p>
    <p><strong>Return:</strong> Rp {{ number_format($purchase->total_return, 0, ',', '.') }}</p>

    <br>
    <p>Thank you for shopping!</p>
</body>
</html>
