<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title ?? 'Barcode Sheet' }}</title>
    <meta charset="UTF-8">

    <style>
        /* ===== PRINT SETUP ===== */
        @media print {
    @page {
        size: A4;
        margin: 10mm;
    }

    body {
        width: 100%;
        margin: 0;
    }
}


        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        /* ===== PAGE WRAPPER ===== */
      /* ===== PAGE WRAPPER ===== */

.barcode-page {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
}

/* ⭐ KEY FIX HERE ⭐ */
.barcode-label {
    width: 48%;                 /* 🔑 48% not 49/50 */
    margin-right: 4%;           /* 🔑 manual gap */
    margin-bottom: 14mm;

    border: 1px solid #ccc;
    padding: 8mm 6mm;
    height: 55mm;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;

    break-inside: avoid;
    page-break-inside: avoid;
}

/* every 2nd item no right margin */
.barcode-label:nth-child(2n) {
    margin-right: 0;
}



        /* ===== TEXT STYLES ===== */
        .store-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .product-name {
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.3;
            margin: 4px 0;
        }

        /* ===== BARCODE ===== */
        .barcode-box {
            width: 100%;
            height: 24mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 6px 0;
        }

        .barcode-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* ===== BOTTOM INFO ===== */
        .product-code {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 4px;
            text-align: center;
        }

        .price {
            font-size: 15px;
            font-weight: bold;
            margin-top: 4px;
            text-align: center;
        }
    </style>
</head>

<body>

@php
    use Milon\Barcode\DNS1D;
    use Illuminate\Support\Str;
@endphp

<div class="barcode-page">

@foreach ($quantity as $key => $qty)
    @for ($i = 0; $i < $qty; $i++)
        @php
            $barcode = new DNS1D();
            $code = $product_code[$key];
        @endphp

        <div class="barcode-label">

            <div class="store-name">
                {{ $setting->app_name ?? 'SAHEB BAZAR' }}
            </div>

            <div class="product-name">
                {{ Str::limit($product_name[$key], 32) }}
            </div>

            <div class="barcode-box">
                <img src="data:image/png;base64,{{
                    $barcode->getBarcodePNG($code, 'C128', 3.2, 120, [0,0,0])
                }}" alt="barcode">
            </div>

            <div class="product-code">
                {{ $code }}
            </div>

            <div class="price">
                Price: Tk {{ number_format($price[$key], 2) }}
            </div>

        </div>
    @endfor
@endforeach

</div>

<script>
    window.print();
</script>

</body>
</html>
