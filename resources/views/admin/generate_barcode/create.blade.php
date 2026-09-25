@extends('layouts.admin.create_app')

@section('content')
<div class="row g-3">

    {{-- PRODUCT SELECT --}}
    <div class="col-md-4 col-sm-6" id="productInfo">
        <label class="form-label"><b>Products</b></label>

        <select class="form-select" id="product_select">
            <option value="">Select Product</option>

            @foreach ($products as $product)
                <option
                    value="{{ $product->code }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price->online_price }}"
                >
                    {{ $product->name }} - {{ $product->code }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- QUANTITY --}}
    <div class="col-md-4 col-sm-6">
        <label class="form-label"><b>Quantity</b></label>
        <input type="number" class="form-control" id="quantity" min="1" placeholder="Quantity">
    </div>

    {{-- ADD BUTTON --}}
    <div class="col-md-4 col-sm-6">
        <label class="form-label text-white"><b>Add</b></label>
        <button type="button" class="btn btn-primary w-100 py-2" id="add_item">
            Add Item
        </button>
    </div>

    {{-- TABLE --}}
    <div class="col-12 mt-3">
        <table class="table table-bordered table-striped align-middle">
            <thead class="bg-primary text-white">
                <tr>
                    <th width="50" class="text-center">SL</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th width="120">Quantity</th>
                    <th width="60" class="text-center">
                        <i class="far fa-trash-alt"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>

</div>
@endsection
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let serial = 0;

    document.getElementById('add_item').addEventListener('click', function () {

        let select = document.getElementById('product_select');
        let quantity = document.getElementById('quantity').value;

        if (!select.value) {
            Swal.fire({
                toast:true, position:'top-end',
                icon:'error', title:'Please select a product',
                showConfirmButton:false, timer:1500
            });
            return;
        }

        if (!quantity || quantity <= 0) {
            Swal.fire({
                toast:true, position:'top-end',
                icon:'error', title:'Quantity required',
                showConfirmButton:false, timer:1500
            });
            return;
        }

        let option = select.options[select.selectedIndex];
        let code   = option.value;
        let name   = option.dataset.name;
        let price  = option.dataset.price;

        serial++;

        let row = `
        <tr id="row_${code}">
            <td class="text-center">${serial}</td>

            <td>
                <input type="hidden" name="price[]" value="${price}">
                <input type="text" class="form-control" readonly name="product_name[]" value="${name}">
            </td>

            <td>
                <input type="text" class="form-control" readonly name="product_code[]" value="${code}">
            </td>

            <td>
                <input type="number" class="form-control" name="quantity[]" value="${quantity}">
            </td>

            <td class="text-center">
                <button type="button"
                    class="btn btn-sm btn-outline-danger remove_item"
                    data-code="${code}"
                    data-name="${name}"
                    data-price="${price}">
                    <i class="far fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        `;

        document.getElementById('tbody').insertAdjacentHTML('beforeend', row);

        // remove from select
        option.remove();
        select.value = '';
        document.getElementById('quantity').value = '';
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove_item')) {

            let btn = e.target.closest('.remove_item');
            let code = btn.dataset.code;
            let name = btn.dataset.name;
            let price = btn.dataset.price;

            // add back to select
            let option = document.createElement('option');
            option.value = code;
            option.dataset.name = name;
            option.dataset.price = price;
            option.text = `${name} - ${code}`;

            document.getElementById('product_select').appendChild(option);

            // remove row
            document.getElementById('row_' + code).remove();
        }
    });

});
</script>
@endpush
