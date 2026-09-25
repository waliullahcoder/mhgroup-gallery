<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $order->order_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border:1px solid #ddd;
            padding:8px;
        }

        th {
            background:#f5f5f5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .company-info h3 {
            margin: 0;
        }

        .invoice-meta {
            text-align: right;
        }

        .badge {
            background: #dc3545;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .no-print {
            text-align: right;
            margin-bottom: 15px;
        }

        .print-btn {
            background:#dc3545;
            color:#fff;
            padding:8px 16px;
            border:none;
            border-radius:4px;
            cursor:pointer;
        }

        .section {
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

{{-- PRINT --}}
<div class="no-print">
    <button onclick="window.print()" class="print-btn">🖨 Print Invoice</button>
</div>

{{-- HEADER --}}
<div class="header">
    <div class="company-info">
        <img src="{{ asset($settings->logo) }}" height="50" alt="Logo">
        <h3>{{ $settings->app_name }}</h3>
        <p>
            📧 {{ $settings->primary_email }} <br>
            ☎ {{ $settings->primary_phone }} <br>
            📍 {{ $settings->address ?? 'Bangladesh' }}
        </p>
    </div>

    <div class="invoice-meta">
        <h2>INVOICE</h2>
        <p>
            <strong>No:</strong> {{ $order->order_number }} <br>
            <strong>Date:</strong> {{ $order->created_at->format('d M Y') }} <br>
            <span class="badge">{{ ucfirst($order->status) }}</span>
        </p>
    </div>
</div>

<hr>

{{-- BILL INFO --}}
<div class="section">
    <table>
        <tr>
            <td width="50%">
                <strong>Bill From:</strong><br>
                {{ $settings->app_name }}<br>
                {{ $settings->primary_email }}<br>
                {{ $settings->primary_phone }}
            </td>
            <td width="50%">
                <strong>Bill To:</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->user->email }}
            </td>
        </tr>
    </table>
</div>

{{-- CALCULATION --}}
@php
    $subtotal = $order->items->sum('total');
    $discount = $order->discount;
    $afterDiscount = $subtotal - $discount;
    $tax = $order->tax;
    $grandTotal = $afterDiscount + $tax;
@endphp

{{-- ITEMS --}}
<div class="section">
    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Product</th>
                <th>Category</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td style="text-align:center;">{{ $item->product->category->name ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->qty }}</td>
                <td style="text-align:center;">৳ {{ number_format($item->price,2) }}</td>
                <td style="text-align:right;">৳ {{ number_format($item->total,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- TOTAL --}}
<div class="section">
    <table>
        <tr>
            <td width="70%"></td>
            <td width="30%">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td style="text-align:right;">৳ {{ number_format($subtotal,2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount {{$settings->discount_type=='percent' ? '('.$settings->discount.'%'.')' : ''}}</td>
                        <td style="text-align:right;">- ৳ {{ number_format($discount,2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax ({{$settings->tax}}%)</td>
                        <td style="text-align:right;">৳ {{ number_format($tax,2) }}</td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <th style="text-align:right;">৳ {{ number_format($grandTotal,2) }}</th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<hr>

<p style="text-align:center;font-size:12px;">
    Thank you for shopping with <strong>{{ $settings->app_name }}</strong> ❤️
</p>

</body>
</html>
