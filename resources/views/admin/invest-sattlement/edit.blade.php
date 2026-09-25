@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <label for="investor_id" class="form-label"><b>Investor <span class="text-danger">*</span></b></label>
            <select name="investor_id" id="investor_id" class="select form-select" data-placeholder="Select Investor" required>
                <option value=""></option>
                @foreach ($additionalData['investors'] as $item)
                    <option value="{{ $item->id }}" {{ $data->investor_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ date('d-m-Y', strtotime(old('date', $data->date))) }}" placeholder="Invest Date" required>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="sattlement_no" class="form-label"><b>Sattlement No. <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="sattlement_no" name="sattlement_no"
                value="{{ $data->sattlement_no }}" readonly placeholder="Sattlement No." required>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="coa_id" class="form-label"><b>Cash Account <span class="text-danger">*</span></b></label>
            <select name="coa_id" id="coa_id" class="form-select select" data-placeholder="Select Cash Account"
                required>
                <option value=""></option>
                @foreach ($additionalData['cashHeads'] as $item)
                    <option value="{{ $item->id }}" {{ old('coa_id', $data->coa_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name . ' - ' . $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <table class="table mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center" width="30">SL#</th>
                        <th>Invest No.</th>
                        <th>Invest Date</th>
                        <th class="px-1 text-end">Invest Qty</th>
                        <th class="px-1 text-end">Invest Amount</th>
                        <th class="px-1 text-center" width="40">
                            <div class="custom-control custom-checkbox mx-auto" style="min-height: 14px;max-height: 14px">
                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                <label for="checkAll" class="custom-control-label"></label>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @include('admin.invest-sattlement.partial.edit-rows', [
                        'invests' => $additionalData['invests'],
                        'data' => $data,
                    ])
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.invest_id:checked').length > 0 && $('.invest_id:checked').length == $('.invest_id').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }

            $(document).on('change', '#investor_id', function(e) {
                $('#checkAll').prop('checked', false);
                var investor_id = $('#investor_id').val();
                $('#tbody').html('');
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        investor_id: investor_id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#tbody').html(response.data);
                            if ($('.invest_id:checked').length > 0 && $('.invest_id:checked')
                                .length == $('.invest_id')
                                .length) {
                                $('#checkAll').prop('checked', true);
                            } else {
                                $('#checkAll').prop('checked', false);
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.invest_id', function(e) {
                if ($('.invest_id:checked').length > 0 && $('.invest_id:checked').length == $('.invest_id')
                    .length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });

            $(document).on('click', '#checkAll', function(e) {
                if ($(this).prop('checked')) {
                    $('.invest_id').prop('checked', true)
                } else {
                    $('.invest_id').prop('checked', false)
                }
            });
        });
    </script>
@endpush
