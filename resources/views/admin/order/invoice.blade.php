<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>

    <style>
        :root {
            --red: #a80000;
            --red-dark: #8f0000;
            --pink: #fbeaea;
            --pink-border: #efd3d3;
            --black: #151515;
        }

        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .serif { font-family: Georgia, 'Times New Roman', serif; }

        .no-print { text-align: right; margin-bottom: 15px; }
        .print-btn {
            background: var(--red); color: #fff; padding: 8px 16px;
            border: none; border-radius: 4px; cursor: pointer;
        }

        table { width: 100%; border-collapse: collapse; }

        /* ---------- HEADER ---------- */
        .header { width: 100%; }
        .header td { vertical-align: top; padding: 0; border: none; }
        .brand-cell { width: 66%; padding-right: 20px; }
        .meta-cell { width: 34%; border-left: 3px solid var(--red) !important; padding-left: 28px !important; }

        .logo { height: 95px; vertical-align: middle; margin-right: 14px; }
        .brand-title {
            display: inline-block; vertical-align: middle;
        }
        .brand-name {
            font-family: Georgia, serif; font-weight: bold;
            font-size: 44px; line-height: 1; margin: 0; color: var(--black);
            letter-spacing: 1px;
        }
        .brand-name span { color: var(--red); }
        .tagline {
            font-family: Georgia, serif; font-size: 17px; letter-spacing: 5px;
            margin-top: 8px; padding-top: 6px; border-top: 2px solid var(--red);
            color: #333;
        }

        .contact-row { margin-top: 12px; font-size: 12px; }
        .contact-row td { padding: 0 10px 0 0; white-space: nowrap; }
        .icon {
            display: inline-block; width: 24px; height: 24px; line-height: 24px;
            border-radius: 50%; background: var(--red); color: #fff;
            text-align: center; font-size: 12px; margin-right: 6px;
        }
        .icon.light { background: var(--pink); color: var(--red); }

        .invoice-title {
            font-family: Georgia, serif; font-weight: bold; font-size: 48px;
            margin: 0 0 4px; letter-spacing: 2px; color: var(--black);
        }
        .title-line { width: 110px; height: 3px; background: var(--red); margin-bottom: 16px; }
        .meta-table td { border: none; padding: 5px 4px; font-size: 14px; }
        .meta-table td.label { font-weight: bold; width: 90px; }
        .meta-table td.colon { width: 15px; }

        /* ---------- CUSTOMER ---------- */
        .customer {
            margin-top: 18px;
            background: var(--pink);
            border: 1px solid var(--pink-border);
            border-left: 6px solid var(--red);
            border-radius: 8px;
            padding: 14px 20px;
        }
        .customer td { border: none; padding: 3px 4px; vertical-align: top; }
        .section-title {
            font-family: Georgia, serif; color: var(--red); font-weight: bold;
            font-size: 16px; letter-spacing: 1px; margin: 0 0 4px;
            text-transform: uppercase;
        }
        .section-line { width: 90px; height: 2px; background: var(--red); margin-bottom: 8px; }
        .cust-label { color: #777; width: 80px; }
        .cust-colon { width: 15px; }
        .avatar {
            width: 56px; height: 56px; border-radius: 50%; background: #f5cccc;
            text-align: center; line-height: 56px; font-size: 26px; color: var(--red);
        }

        /* ---------- ITEMS ---------- */
        .items {
            margin-top: 18px;
            border-radius: 8px; overflow: hidden;
            border: 1px solid #ddd;
        }
        .items th {
            background: var(--black); color: #fff;
            font-family: Georgia, serif; text-transform: uppercase;
            font-size: 13px; padding: 14px 8px;
            border: none; border-right: 1px solid var(--red);
        }
        .items th:last-child { border-right: none; }
        .items td {
            padding: 14px 8px; border: none;
            border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;
            text-align: center;
        }
        .items td:last-child { border-right: none; }
        .items td.left { text-align: left; }

        /* ---------- NOTE + TOTALS ---------- */
        .bottom { margin-top: 18px; }
        .bottom > tbody > tr > td { border: none; padding: 0; vertical-align: top; }
        .note-box {
            border: 1px solid #ddd; border-radius: 8px;
            padding: 14px 18px; min-height: 150px; margin-right: 16px;
        }
        .note-text { margin-top: 6px; color: #444; line-height: 1.5; }

        .totals { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .totals td { border: none; padding: 11px 16px; font-size: 14px; }
        .totals td.val { text-align: right; }
        .totals tr.highlight td { background: var(--pink); font-weight: bold; }
        .totals tr.grand td {
            background: var(--red-dark); color: #fff;
            font-family: Georgia, serif; font-size: 24px; padding: 14px 16px;
        }
        .totals tr.grand td.val { font-size: 28px; font-weight: bold; }

        /* ---------- FOOTER ---------- */
        .footer { margin-top: 22px; border-top: 2px solid var(--red); padding-top: 14px; }
        .footer td { border: none; vertical-align: middle; padding: 0 8px; font-size: 12px; }
        .thanks {
            font-family: 'Brush Script MT', 'Segoe Script', cursive; font-style: italic;
            font-size: 30px; color: var(--red); line-height: 1;
        }
        .thanks-sub { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; margin-top: 4px; }
        .guarantee {
            border-left: 1px solid var(--red) !important;
            font-family: Georgia, serif; font-size: 10px; letter-spacing: 2px;
            text-transform: uppercase; line-height: 1.8;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

{{-- PRINT --}}
<div class="no-print">
    <button onclick="window.print()" class="print-btn">🖨 Print Invoice</button>
</div>

{{-- CALCULATION --}}
@php
    $subtotal      = $order->items->sum('total');
    $discount      = $order->discount ?? 0;
    $afterDiscount = $subtotal - $discount;
    $tax           = $order->tax ?? 0;
    $grandTotal    = $afterDiscount + $tax;
    $total         = $order->total;                 // final payable amount saved in DB
    $advance       = $order->advance_payment ?? 0;  // <-- change field name if different
    $delivery      = $total - $grandTotal + $advance;
    if ($delivery < 0) { $delivery = 0; }
@endphp

{{-- HEADER --}}
<table class="header">
    <tr>
        <td class="brand-cell">
            <img src="{{ asset($settings->logo) }}" class="logo" alt="Logo">
            <!-- <div class="brand-title">
                @php
                    $nameParts = explode(' ', $settings->app_name ?? 'MH GALLERY', 2);
                @endphp
                <h1 class="brand-name"><span>{{ $nameParts[0] }}</span> {{ $nameParts[1] ?? '' }}</h1>
                <div class="tagline">House of Royal Fragrance</div>
            </div> -->

            <table class="contact-row">
                <tr>
                    <td><span class="icon">☎</span>{{ $settings->primary_phone }}</td>
                    <td><span class="icon">✉</span>{{ $settings->primary_email }}</td>
                    <td><span class="icon">📍</span>{{ $settings->address ?? 'Bangladesh' }}</td>
                </tr>
            </table>
        </td>

        <td class="meta-cell">
            <h2 class="invoice-title">INVOICE</h2>
            <div class="title-line"></div>
            <table class="meta-table">
                <tr>
                    <td class="label">Invoice No</td>
                    <td class="colon">:</td>
                    <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td class="label">Date</td>
                    <td class="colon">:</td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- CUSTOMER INFO --}}
<div class="customer">
    <table>
        <tr>
            <td width="80" style="vertical-align:middle;"><div class="avatar">👤</div></td>
            <td>
                <div class="section-title">Customer Information</div>
                <div class="section-line"></div>
                <table>
                    <tr>
                        <td class="cust-label">Name</td>
                        <td class="cust-colon">:</td>
                        <td>{{ $order->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="cust-label">Phone</td>
                        <td class="cust-colon">:</td>
                        <td>{{ $order->user->phone }}</td>
                    </tr>
                    <tr>
                        <td class="cust-label">Address</td>
                        <td class="cust-colon">:</td>
                        <td>{{ $order->user->address }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- ITEMS --}}
<div class="items">
    <table>
        <thead>
            <tr>
                <th width="6%">SL</th>
                <th width="15%">Code</th>
                <th style="text-align:left;">Product</th>
                <th width="12%">Size/Unit</th>
                <th width="8%">Qty</th>
                <th width="12%">Price</th>
                <th width="12%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product->code }}</td>
                <td class="left">{{ $item->product->name }}</td>
                <td>{{ App\Models\ProductVariant::where('id', $item->product_variant_id)->first()?->variant }} {{ $item->product->uom->name ?? '-' }}</td>
                <td>{{ $item->qty }}</td>
                <td>৳{{ number_format($item->price, 2) }}</td>
                <td>৳{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- NOTE + TOTALS --}}
<table class="bottom">
    <tr>
        <td width="58%">
            <div class="note-box">
                <div class="section-title">Customer Note</div>
                <div class="section-line"></div>
                <div class="note-text">{{ $order->note ?? '' }}</div>
            </div>
        </td>
        <td width="42%">
            <table class="totals">
                <tr>
                    <td>Subtotal</td>
                    <td class="val">৳{{ number_format($subtotal, 2) }}</td>
                </tr>

                @if($discount > 0)
                <tr>
                    <td>Discount {{ ($settings->discount_type ?? '') == 'percent' ? '('.$settings->discount.'%)' : '' }}</td>
                    <td class="val">- ৳{{ number_format($discount, 2) }}</td>
                </tr>
                @endif

                @if($tax > 0)
                <tr>
                    <td>Tax ({{ $settings->tax }}%)</td>
                    <td class="val">৳{{ number_format($tax, 2) }}</td>
                </tr>
                @endif

                @if($advance > 0)
                <tr class="highlight">
                    <td>Advance Payment</td>
                    <td class="val">৳{{ number_format($advance, 2) }}</td>
                </tr>
                @endif

                <tr>
                    <td>Delivery Charge</td>
                    <td class="val">৳{{ number_format($delivery, 2) }}</td>
                </tr>

                <tr class="grand">
                    <td>Grand Total</td>
                    <td class="val">৳{{ number_format($total, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- FOOTER --}}
<div class="footer">
    <table>
        <tr>
            <td width="24%">
                <div class="thanks">Thank You!</div>
                <div class="thanks-sub">For shopping with {{ $settings->app_name }}</div>
            </td>
            <td><span class="icon">☎</span>{{ $settings->primary_phone }}</td>
            <td><span class="icon">✉</span>{{ $settings->primary_email }}</td>
            <td><span class="icon">📍</span>{{ $settings->address ?? 'Bangladesh' }}</td>
            <td class="guarantee" width="22%">
                Authentic Fragrance<br>
                Premium Quality<br>
                Your Trust, Our Priority
            </td>
        </tr>
    </table>
</div>

</body>
</html>