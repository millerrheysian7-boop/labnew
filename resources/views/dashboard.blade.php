@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Total Products</div>
                <div class="card-body">
                    <h3>{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Low Stock</div>
                <div class="card-body">
                    <h3>{{ $lowStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Out of Stock</div>
                <div class="card-body">
                    <h3>{{ $outOfStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Inventory Value</div>
                <div class="card-body">
                    <h3>${{ number_format($totalValue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .hw-chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 10px rgba(138, 95, 65, 0.12);
            height: 100%;
        }
        .hw-chart-card h5 {
            color: #8A5F41;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hw-chart-wrapper {
            position: relative;
            height: 240px;
        }
        @media (max-width: 767.98px) {
            .hw-chart-wrapper {
                height: 200px;
            }
        }
    </style>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="hw-chart-card">
                <h5><i class="bi bi-graph-up"></i> General Overview</h5>
                <div class="hw-chart-wrapper">
                    <canvas id="generalOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="hw-chart-card">
                <h5><i class="bi bi-bar-chart-fill"></i> Inventory Value by Category</h5>
                <div class="hw-chart-wrapper">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="hw-chart-card">
                <h5><i class="bi bi-pie-chart-fill"></i> Stock Status</h5>
                <div class="hw-chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="hw-chart-card">
                <h5><i class="bi bi-trophy-fill"></i> Top 5 Products by Stock Value</h5>
                <div class="hw-chart-wrapper">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recently Added Products</div>
                <ul class="list-group list-group-flush">
                    @forelse ($recentProducts as $product)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $product->name }}</span>
                            <span class="text-muted">{{ $product->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No products yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Inventory Value by Category</div>
                <ul class="list-group list-group-flush">
                    @foreach ($categorySummary as $row)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $row->category }} ({{ $row->count }})</span>
                            <span>${{ number_format($row->value, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Brands</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Brand</th><th>Products</th><th>Units</th><th>Value</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($brandSummary as $row)
                                <tr>
                                    <td>{{ $row->brand }}</td>
                                    <td>{{ $row->count }}</td>
                                    <td>{{ $row->units }}</td>
                                    <td>${{ number_format($row->value, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No brands yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Categories</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Category</th><th>Products</th><th>Units</th><th>Value</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($categoryDetails as $row)
                                <tr>
                                    <td>{{ $row->category }}</td>
                                    <td>{{ $row->count }}</td>
                                    <td>{{ $row->units }}</td>
                                    <td>${{ number_format($row->value, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No categories yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    window.dashboardData = @json($chartData);
</script>
<script src="{{ asset('js/dashboard-charts.js') }}"></script>
@endsection
