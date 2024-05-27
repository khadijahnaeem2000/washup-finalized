@extends('layouts.master')
@section('title', 'Order')
@section('content')
    @include('../sweet_script')
    <style>
        .checkbox-label {
            visibility: hidden;
        }
    </style>
    {!! Form::open(['id' => 'form']) !!}
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Inspect Order (Content)</h3>
                    </div>
                    <div class="card-toolbar">
                        <div style="margin-right: 10px">
                            <h4 style="text-align: center">Distribution Hub</h4>
                            {!! Form::select('hub_id', $hubs, null, array('class' => 'form-control', 'autofocus' => '', 'required' => 'true', 'id' => 'hub_id')) !!}
                        </div>
                        <div style="margin-right: 10px">
                            <h4 style="text-align: center">Action</h4>
                            <button type="submit" id="btn_receive" name="btn_receive" class="btn btn-success font-weight-bolder">Received to Hub <i class="fas fa-check"></i></button>
                        </div>
                          @can('waive')
                        <div style="margin-top: 27px; margin-right:10px">
                            <a href="#" class="btn btn-primary" id="waverDeliveryBtn">Waive Delivery</a>
                        </div>
                        @endcan
                    </div>
                </div>
                <?php $checkvar = false; ?>
                @can('special_polybag-print')
                    <?php $checkvar = true; ?>
                @endcan
                @if(!$checkvar)
                    <style>
                        .chk_prm {
                            display: none;
                        }
                    </style>
                @endif
                <div class="card-body">
                    <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%">
                                            <i class="fas fa-check" id="selectAll" class="btn btn-success btn-xs"></i>
                                        </th>
                                        <th width="2%">#</th>
                                        <th>Order#</th>
                                        <th>Name</th>
                                        <th>Contact#</th>
                                        <th>Pickup Date</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th title="Order Packed">
                                            <i class="fas fa-box"></i>
                                        </th>
                                        <th width="10%">Inspect</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>
    {!! Form::close() !!}
    <script>
        $(document).ready(function () {
            var hub_id = $('#hub_id').val();
            var order_table;

            if (hub_id > 0) {
                order_table = $('#myTable').DataTable({
                    "aaSorting": [],
                    "processing": true,
                    "serverSide": true,
                    "ajax": "{{ url('order_inspect_list') }}" + '/' + hub_id,
                    "method": "GET",
                    "columns": [
                        {"data": "checkbox", orderable: false, searchable: false},
                        {"data": "srno"},
                        {"data": "id"},
                        {"data": "name"},
                        {"data": "contact_no"},
                        {"data": "pickup_date"},
                        {"data": "delivery_date"},
                        {"data": "status_name"},
                        {"data": "polybags_printed"},
                        {"data": "action", orderable: false, searchable: false}
                    ]
                });

                // BEGIN:: Btn Assign
              $('#btn_receive').click(function (e) {
    console.log("hello");
    e.preventDefault();
 var selectedIDs = [];
    $('input[type="checkbox"]:checked').each(function () {
        selectedIDs.push($(this).closest('tr').find('td:nth-child(3)').text()); // Adjust the column index accordingly
    });

    console.log("Selected Order IDs:", selectedIDs);

    console.log("Selected Order IDs:", selectedIDs);
        var formData = {
        _token: '{{ csrf_token() }}',
        hub_id: $('#hub_id').val(),
        order_id:selectedIDs,
    };
    $.ajax({
        data:formData,
        url: "{{ route('order_inspects.store') }}",
        type: "POST",
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                console.log("hello success");
                toastr.success(data.success);
                var hub_id = $('#hub_id').val(); // Simplified way to get value
                order_table.ajax.url('order_inspect_list/' + hub_id).load();
            } else {
                console.log("fetching");
                var hub_id = $('#hub_id').val(); // Simplified way to get value
                order_table.ajax.url('order_inspect_list/' + hub_id).load();
                toastr.error(data.error);
            }
        },
        error: function (data) {
            console.log("error");
            console.log('Error:', data);
        }
    });
});

// Rest of your JavaScript code...

                $('#waverDeliveryBtn').click(function (event) {
                    event.preventDefault();
                    var selectedIDs = [];
                    $('.checkbox-inline input:checkbox:checked').each(function () {
                        selectedIDs.push($(this).attr('name').split('[')[1].split(']')[0]);
                    });

                    if (selectedIDs.length > 0) {
                        $.ajax({
                            url: "{{ route('order_inspects.create') }}",
                            type: "POST",
                            data: {
                                ids: selectedIDs,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                if (data.success) {
                                    toastr.success("Successfully updated order to wavier delivery");
                                    setTimeout(function () {
                                        location.reload();
                                    }, 4000);
                                } else {
                                     toastr.error("User have not permission for this page access.");
                                }
                            },
                            error: function (xhr, status, error) {
                               
                                console.error(xhr.responseText);
                            }
                        });
                    } else {
                        alert('Please select at least one order to waver delivery.');
                    }
                });

                $('#selectAll').click(function (event) {
                    event.stopPropagation();
                    $('input:checkbox').prop('checked', !$(this).prop('checked'));
                });
            } else {
                $('#hub_id').append('<option value = "0">--- No Hub ---</option>');
            }
        });
    </script>
@endsection
