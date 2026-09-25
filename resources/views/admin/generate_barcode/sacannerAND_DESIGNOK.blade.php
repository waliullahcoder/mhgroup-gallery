<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title ?? 'Barcode Print' }}</title>
    <meta charset="UTF-8">

    <style>
        @media print {
            @page {
                size: A4;
                margin: 8mm;
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
        }

        /* LABEL : 38mm x 35mm */
        .barcode-label {
            width: 38mm;
            height: 35mm;
            padding: 1.5mm;
            border: none;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;

            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* TEXT */
        .store-name {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
        }

        .product-name {
            font-size: 10px;
            text-align: center;
            line-height: 1.1;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* BARCODE */
        .barcode-box {
            width: 100%;
            height: auto;          /* ⬅ slightly smaller */
            margin: 0;             /* ⬅ gap remove */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* CODE */
        .product-code {
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 3.6px;   /* ✅ controlled character space */
            margin-top: 0.5mm;       /* ⬅ tight gap */
            text-align: center;
        }

        /* PRICE */
        .price {
            font-size: 12px;
            margin-top: 0.3mm;       /* ⬅ tight gap */
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
