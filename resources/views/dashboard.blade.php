@extends('layouts.app')
@section('title', 'Dashboard - W Mart')

@section('content')
    <div class="mb-4">
        <div class="breadcrumb">
            <span class="breadcrumb-item">
                <i class="bi bi-house-door"></i>
                <i class="bi bi-arrow-right-short"></i> Dashboard
            </span>
        </div>
        <h2 class="page-title">Dashboard</h2>
    </div>

    @if (Auth::user()->isAdmin())
        {{-- ADMIN VIEW: Chart Section --}}
        <div class="card shadow-sm border mb-4 p-4">
            <div class="d-flex gap-4" style="align-items: flex-start;">
                <!-- Bar Chart -->
                <div style="flex: 2;">
                    <canvas id="chartjs-bar" style="max-height: 300px;"></canvas>
                </div>

                <!-- Pie Chart -->
                <div style="flex: 1; max-width: 300px;">
                    <canvas id="chartjs-pie" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    @else
     {{-- STAFF VIEW --}}
    <div class="d-flex justify-content-center mt-4">
        <div class="card shadow-sm border p-4" style="max-width: 500px; width: 100%;">
            <h4 class="mb-3 text-center">Welcome, Officer!</h4>
            <h5 class="text-center">Today's Total Sales</h5>
            <h1 class="display-4 fw-bold text-primary text-center">{{ $totalPurchasesToday ?? 0 }}</h1>
            <p class="text-muted text-center">The total number of sales that occurred today.</p>
            <p class="text-muted small text-center">
                Last Purchase:
                {{ $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->format('d M Y H:i') : '-' }}
            </p>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    // Search Button
    document.addEventListener("DOMContentLoaded", function () {
        const searchButton = document.getElementById('search-button');
        const searchInput = document.getElementById('search-input');
        searchButton.addEventListener('click', () => {
            const inputValue = searchInput.value;
            alert("Search: " + inputValue);
        });

        // Bar Chart
        const barLabels = @json($barLabels);
        const barData = @json($barData);

        const pieLabels = @json($pieLabels);
        const pieData = @json($pieData);
        
        new Chart(document.getElementById("chartjs-bar"), {
            type: "bar",
            data: {
                labels: barLabels,
                datasets: [{
                    label: "Jumlah Penjualan",
                    backgroundColor: "#0d6efd",
                    borderColor: "#0d6efd",
                    data: barData
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Pie chart
        new Chart(document.getElementById("chartjs-pie"), {
            type: "pie",
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieLabels.map((_, i) => `hsl(${i * 15}, 70%, 60%)`)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection