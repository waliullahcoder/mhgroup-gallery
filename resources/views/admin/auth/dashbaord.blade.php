@extends('layouts.admin.app')

@section('content')

<style>
.info-card { border-radius: 12px; transition: transform 0.2s; }
.info-card:hover { transform: translateY(-5px); }
.card-body { display: flex; justify-content: space-between; align-items: center; }
.card-content p { font-size: 0.9rem; color: #666; }
.card-count { font-size: 1.5rem; font-weight: 600; }
.card-icon { width: 50px; height: 50px; font-size: 24px; }
.card-header { font-weight: 600; font-size: 1rem; }
</style>

<div class="container-fluid">
    <!-- Info Cards -->
    <div class="row g-3 mb-4 info-cards">
        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.expense.index') }}">
                <div class="card-body">
                    <div>
                        <p>Total Expense</p>
                        <h3 class="card-count">৳{{ number_format($dashboardData['totalExpense'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.sales.index') }}">
                <div class="card-body">
                    <div>
                        <p>Total Sales</p>
                        <h3 class="card-count">৳{{ number_format($dashboardData['totalSales'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">sell</span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.orders.index') }}">
                <div class="card-body">
                    <div>
                        <p>Total Orders</p>
                        <h3 class="card-count">{{ number_format($dashboardData['totalOrders'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.expense.index') }}">
                <div class="card-body">
                    <div>
                        <p>Daily Expense</p>
                        <h3 class="card-count">৳{{ number_format($dashboardData['dailyExpense'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.sales.index') }}">
                <div class="card-body">
                    <div>
                        <p>Daily Sales</p>
                        <h3 class="card-count">৳{{ number_format($dashboardData['dailySales'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">currency_exchange</span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card info-card shadow-sm">
                <a href="{{ route('admin.orders.index') }}">
                <div class="card-body">
                    <div>
                        <p>Daily Orders</p>
                        <h3 class="card-count">{{ number_format($dashboardData['dailyOrders'] ?? 0) }}</h3>
                    </div>
                    <div class="card-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">Monthly Sales</div>
                <div class="card-body">
                    <canvas id="salesBarChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">Monthly Expense</div>
                <div class="card-body">
                    <canvas id="expenseLineChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('backend/js/chart.js') }}"></script>
<script>
const months = @json($dashboardData['months'] ?? []);
const monthlySales = @json($dashboardData['monthlySales'] ?? []);
const monthlyExpense = @json($dashboardData['monthlyExpense'] ?? []);

// Sales Bar Chart
const salesCtx = document.getElementById('salesBarChart').getContext('2d');
new Chart(salesCtx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Monthly Sales',
            data: monthlySales,
            backgroundColor: '#4CAF50'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Monthly Sales' }
        },
        scales: { y: { beginAtZero: true } }
    }
});

// Expense Line Chart
const expenseCtx = document.getElementById('expenseLineChart').getContext('2d');
new Chart(expenseCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Monthly Expense',
            data: monthlyExpense,
            borderColor: '#F44336',
            backgroundColor: 'rgba(244, 67, 54, 0.2)',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: '#F44336'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Monthly Expense' }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endsection