<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title ?? 'Barcode Sheet' }}</title>
    <meta charset="UTF-8">

    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
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

        /* ===== PAGE GRID ===== */
        .barcode-page {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* ✅ 2 COLUMN */
            gap: 12mm 10mm; /* row-gap | column-gap */
        }

        /* ===== SINGLE LABEL ===== */
        .barcode-label {
            border: 1px solid #ccc;
            padding: 8mm 6mm;
            height: 55mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .store-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 4px;
        }

        .product-name {
            font-size: 12px;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .barcode-box {
            width: 100%;
            height: 22mm;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 6px 0;
        }

        .barcode-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .product-code {
            font-size: 14px;
            letter-spacing: 1.5px;
            margin-top: 6px;
            margin-bottom: 6px;
            text-align: center;
            font-weight: bold;
        }

        .price {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding-top: 4px;
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
                {{ Str::limit($product_name[$key], 30) }}
            </div>

            <div class="barcode-box">
                <img src="data:image/png;base64,{{
                    $barcode->getBarcodePNG($code, 'C128', 3, 110, [0,0,0])
                }}" alt="barcode">
            </div>

            <div class="product-code">
                {{ $code }}
            </div>

            <div class="price">
                Price: Tk. {{ number_format($price[$key], 2) }}
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
