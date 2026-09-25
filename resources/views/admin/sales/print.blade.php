
@extends('layouts.admin.print_preview')
@section('content')
<!-- INVOICE TITLE -->

<div class="invoice-title">
<h2> INVOICE #{{$data->invoice}}</h2>
</div>


<!-- CLIENT + INVOICE INFO -->

<div class="row invoice-info">

<div class="col-xs-6">

<h4>Bill To</h4>
@php
    $user= App\Models\User::where('id',$data->client->user_id)->first();
@endphp
<p>
<strong>{{ $data->client->name ?? '' }}</strong><br>
{{$data->client->address?? $user->address}} <br>
Phone: {{$data->client->phone??'N/A'}} <br>
Email: {{$data->client->email??'N/A'}}
</p>

</div>


<div class="col-xs-6">

<table class="table table-bordered">

<tr>
<th width="40%">Invoice No</th>
<td>{{ $data->invoice }}</td>
</tr>

<tr>
<th>Date</th>
<td>{{ date('d-m-Y', strtotime($data->date)) }}</td>
</tr>

<tr>
<th>Sales Type</th>
<td>{{ $data->sale_type }}</td>
</tr>

<tr>
<th>Remarks</th>
<td>{{ $data->remarks }}</td>
</tr>

</table>

</div>

</div>


<!-- BOOKS TABLE -->

<table class="table table-bordered invoice-table">

<thead>

<tr>

<th width="40">SL</th>
<th>Book Name</th>
<th>Edition</th>
<th class="text-right">Price</th>
<th class="text-right">Commission %</th>
<th class="text-right">Net Price</th>
<th class="text-right">Qty</th>
<th class="text-right">Amount</th>

</tr>

</thead>

<tbody>

@foreach ($data->list as $row)

<tr>

<td class="text-center">{{ $loop->iteration }}</td>

<td>{{ $row->product->name ?? '' }}</td>

<td>{{ $row->edition->name ?? '' }}</td>

<td class="text-right">{{ number_format($row->price,2) }}</td>

<td class="text-right">{{ $row->commission }}%</td>

<td class="text-right">{{ number_format($row->rate,2) }}</td>

<td class="text-right">{{ $row->qty }}</td>

<td class="text-right">{{ number_format($row->amount,2) }}</td>

</tr>

@endforeach

</tbody>

</table>



<!-- TOTAL SECTION -->

<div class="row total-area">

<div class="col-xs-6">

<p>
<strong>In Words :</strong>  
{{ \App\HelperClass::convertNumber($data->net_amount) }} Taka Only
</p>

</div>


<div class="col-xs-6">

<table class="table table-bordered">

<tr>
<th>Total</th>
<td class="text-right">
৳{{ number_format($data->amount,2) }}
</td>
</tr>


<tr>
<th>Discount</th>
<td class="text-right">
৳{{ number_format($data->discount,2) }}
</td>
</tr>
<tr>
<th>Tax</th>
<td class="text-right">
৳{{ number_format($data->tax_amount,2) }}
</td>
</tr>


<tr class="grand-total">
<th>Net Amount</th>
<td class="text-right">
৳{{ number_format($data->net_amount,2) }}
</td>
</tr>

</table>

</div>

</div>



<!-- SIGNATURE -->

<div class="row signature">

<div class="col-xs-6 text-center">

<div class="sign-line"></div>
<p>Received By</p>

</div>


<div class="col-xs-6 text-center">

<div class="sign-line"></div>
<p>Prepared By</p>
<p>{{ @$data->user->name }}</p>

</div>

</div>



<!-- FOOTER -->

<div style="margin-top:40px;font-size:12px">

Print Date & Time :
{{ date('d-m-Y h:i:s A') }}

</div>
@endsection