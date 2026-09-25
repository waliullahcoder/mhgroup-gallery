<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>{{ $title ?? 'Sales Invoice' }}</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
<style>

body{
     font-family: 'SolaimanLipi', sans-serif;
    font-size:13px;
    padding:20px;
}

.logo{
    height:70px;
}

.company-name{
    font-size:22px;
    font-weight:bold;
}

.invoice-title{
    text-align:center;
    margin:20px 0;
    border-top:2px solid #000;
    border-bottom:2px solid #000;
    padding:8px;
}

.invoice-title h2{
    margin:0;
    font-weight:bold;
}

.invoice-info{
    margin-top:20px;
}

.invoice-table thead{
    background:#f5f5f5;
}

.invoice-table th{
    text-align:center;
}

.invoice-table td{
    vertical-align:middle !important;
}

.total-area{
    margin-top:20px;
}

.grand-total{
    font-size:16px;
    font-weight:bold;
    background:#f2f2f2;
}

.sign-line{
    border-top:1px solid #000;
    width:200px;
    margin:70px auto 5px;
}

.signature{
    margin-top:40px;
}

.print-btn{
    background:#35c3dc;
    color:#fff;
    border:none;
    padding:8px 16px;
    border-radius:4px;
}

.no-print{
    text-align:right;
    margin-bottom:10px;
}

@media print{
    .no-print{
        display:none;
    }
}

</style>
</head>

<body>

<div class="no-print">
<button onclick="window.print()" class="print-btn">🖨 Print Invoice</button>
</div>


<!-- HEADER -->

<div class="row">
<div class="col-xs-6">
<img src="{{ asset($settings->logo) }}" class="logo">
</div>

<div class="col-xs-6 text-right">

<h3 class="company-name">{{$settings->title??'N/A'}}</h3>

<p>
{{$settings->address??'N/A'}} <br>
Phone: {{$settings->primary_phone??'N/A'}} <br>
Email: {{$settings->primary_email??'N/A'}}
</p>

</div>
</div>

 @yield('content')
</body>

</html>