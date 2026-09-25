<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title ?? 'Barcode Print' }}</title>
    <meta charset="UTF-8">

    <style>
        @media print {
            @page {
                size: 38mm 25mm;
                margin: 0;
                padding: 0;
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

        /* PAGE */
        .barcode-page {
            display: flex;
            flex-wrap: wrap;
            gap: 3mm;
            margin-top:1px;
        }

        /* LABEL : 38mm x 35mm */
        .barcode-label {
            width: 38mm;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-before: always;
            break-before: always;
        }
        
        /* TEXT */
        .store-name {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-top: 0.5mm;
            margin-bottom: 0.1mm;
            text-transform: uppercase;
        }

        .product-name {
            font-size: 9px;
            text-align: center;
            line-height: 1.1;
            margin-bottom: 0.3mm;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* BARCODE */
        .barcode-box {
            width: 100%;
            margin: 0;             /* ⬅ gap remove */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-box img {
            width: 100%;
            height: 10mm;
            object-fit: contain;
        }

        /* CODE */
        .product-code {
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 3.6px;   /* ✅ controlled character space */
            text-align: center;
        }

        /* PRICE */
        .price {
            font-size: 11px;
            text-align: center;
            font-weight: bold;
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
            $getresult = substr(@$product_code[$key], 0);
        @endphp

        <div class="barcode-label">

            <div class="store-name">
                {{ $setting->app_name ?? 'SAHEB BAZAR' }}
            </div>

            <div class="product-name">
                {{ Str::limit($product_name[$key], 28) }}
            </div>

            <div class="barcode-box">
                   <img src="data:image/png;base64,{{ $barcode->getBarcodePNG($getresult, 'C128', 4, 150, $color = [0, 0, 0]) }}"
                                    alt="barcode" />
            </div>

            <div class="product-code">
                {{ $code }}
            </div>

            <div class="price">
                Tk. {{ number_format($price[$key], 2) }}
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
