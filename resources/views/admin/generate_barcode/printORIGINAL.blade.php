<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ @$title }}</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('backend/css/old.bootstrap.min.css') }}" rel="stylesheet"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <style>
        @media screen,
        print {
            @page {
                size: 38mm 25mm;
                margin: 0;
                padding: 0;
            }

            .text-nowrap {
                white-space: nowrap;
            }

            tfoot,
            thead {
                color: #fff;
                background-color: #00ABBD;
            }

            .overflow-hidden {
                overflow: hidden;
            }

            .d-inline-block {
                display: inline-block;
            }

            .table th,
            .table td {
                padding: 3px 10px !important;
            }

            .table tfoot th,
            .table tfoot td,
            .table thead th {
                font-family: 'PT Serif', serif;
                padding: 0px 10px 5px !important;
            }

            .table th,
            .table td {
                padding: 10px !important;
                border: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                color: #333;
                /* No margin for thermal receipts */
            }

            /* Optional: Custom styles for receipt elements */
            .receipt {
                font-family: Arial, sans-serif;
                font-size: 12px;
                /* Adjust as needed */
            }

            .d-flex {
                display: flex;
            }

            .justify-content-between {
                justify-content: space-between
            }

            .barcode-label {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                page-break-before: always;
                break-before: always;
                position: relative;
                max-width: 500px;
            }

            .barcode-label>div {
                width: 100%;
                padding-left: 2mm;
                padding-right: 2mm;
            }

            img {
                width: 90%;
                margin-inline: auto;
                display: block;
                height: 8mm;
                print-color-adjust: exact;
                /* Ensures printer keeps exact colors */
                -webkit-print-color-adjust: exact;
                margin-top: 5px;
                margin-bottom: 2px;
            }

            .barcode_wrapper {
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-height: 25mm;
            }

            .horizontal_text {
                font-size: 10px;
                text-transform: uppercase;
                line-height: 1.2;
                font-weight: bold;
            }

            .name_text {
                width: 100%;
            }

            .price_text,
            .code_text {
                width: 100%;
                text-align: center;
            }

            .price_text {}
        }
    </style>
    @stack('css')
</head>

<body>
    @php
        error_reporting(0);
        use Milon\Barcode\DNS1D;
    @endphp

    @foreach ($quantity as $key => $qty)
        <div>
            <div>
                @for ($i = 0; $i < $qty; $i++)
                    <div class="barcode-label">
                        <div class="text-center">
                            @php
                                $getresult = substr(@$product_code[$key], 0);
                                $barcode = new DNS1D();
                            @endphp
                            <div class="barcode_wrapper" style="width: 100%; text-align: center;">
                                <div class="horizontal_text">{{ @$setting->app_name }}</div>
                                <div class="text name_text" style="font-size: 8px; text-transform: uppercase;">
                                    {{ Str::limit(@$product_name[$key], 23) }}</div>
                                <img src="data:image/png;base64,{{ $barcode->getBarcodePNG($getresult, 'C128', 4, 150, $color = [0, 0, 0]) }}"
                                    alt="barcode" />
                                <div class="text-center code_text" style="font-size: 12px;">
                                    <div>{{ @$product_code[$key] }}</div>
                                </div>
                                <div class="text-center price_text" style="font-size: 12px;">
                                    <div>Price: {{ number_format(@$price[$key], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endforeach

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
