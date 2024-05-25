@extends('layouts.master')
@section('title','Rider Compensation')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>

                     @can('customer-create')
                        <div class="card-toolbar">
                            <a  href="{{ route('rider_incentives.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new incentive </a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%" >#</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Pickup Rate</th>
                                        <th>Dropoff Rate</th>
                                        <th>Pickdrop Rate</th>
                                        <th>kilometer Rate</th>
                                        <th>Status</th>
                                        <th>Default</th>
                                        <th width="10%" >Action</th>
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
    <script>
        $(document).ready(function () {  
     
            $('#myTable').DataTable({
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                
                "select":true,
                "ajax": "{{ route('rider_incentives_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "DT_RowIndex"},
                    {"data": "id", "visible": false},
                    {"data": "name"},
                    {"data": "pickup_rate"},
                    {"data": "drop_rate"},
                    {"data": "pickdrop_rate"},
                    {"data": "kilometer"},
                       {
                "data": "status",
                "render": function (data) {
                    var checked = data == 1 ? 'checked' : '';
                    return '<span class="switch switch-outline switch-icon switch-primary">' +
                        '<label>' +
                        '<input type="checkbox" class="form-control status-checkbox" data-status="' + data + '" ' + checked + '>' +
                        '<span></span>' +
                        '</label>' +
                        '</span>';
                }
            },
             {
                "data": "default_rider",
                "render": function (data) {
                    var checked = data == 1 ? 'checked' : '';
                    return '<span class="switch switch-outline switch-icon switch-primary">' +
                        '<label>' +
                        '<input type="checkbox" class="form-control default-checkbox" data-status="' + data + '" ' + checked + '>' +
                        '<span></span>' +
                        '</label>' +
                        '</span>';
                }
            },
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
        });

        
   $('#myTable').on('change', 'input.status-checkbox', function () {
    console.log("hello");
      var row = $(this).closest('tr');
    
     var rowData = $('#myTable').DataTable().row(row).data();
    
    // Retrieve the ID from the row data
    var serviceId = rowData.id;
    
    // Log the ID to ensure it's retrieved correctly
    console.log("Service ID:", serviceId);

    var status = $(this).prop('checked') ? 1 : 0;

    // AJAX request to update status
    $.ajax({
        url: "{{ route('update_rider_incentives_status') }}",
        method: "POST",
        data: {
            id: serviceId,
            status: status,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function(xhr) {
            // You can add loading indicators or disable elements before sending the request
            console.log("sending");
        },
        success: function (response) {
            // Handle success response if needed
            console.log("Successfully updated status:", response);
        },
        error: function (xhr, status, error) {
            // Handle error if needed
            console.error( error);
            console.log(status);
        },
        complete: function(xhr, status) {
            // This function will always be executed after success/error callbacks
            // You can remove loading indicators or enable elements here
            console.log("error or success");
        }
    });
    
    });
    
$('#myTable').on('change', 'input.default-checkbox', function () {
    console.log("hello");

    var row = $(this).closest('tr');
    var rowData = $('#myTable').DataTable().row(row).data();
    var Id = rowData.id;
    var status = $(this).prop('checked') ? 1 : 0;

    // Keep track of the current checkbox
    var currentCheckbox = $(this);

    // AJAX request to update default status
    $.ajax({
        url: "/update_rider_incentives_default",
        method: "POST",
        data: {
            id: Id,
            status: status,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function(xhr) {
            console.log("sending");
        },
        success: function (response) {
            console.log("Successfully updated default:", response);
            
            // If success, uncheck all other default checkboxes except the one that was just clicked
            if (response.success) {
                $('.default-checkbox').each(function() {
                    if ($(this).data('status') != currentCheckbox.data('status')) {
                        $(this).prop('checked', false);
                    }
                });
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            console.log(status);
        },
        complete: function(xhr, status) {
            console.log(status);
        }
    });
});



$('.delete_alls').on('click', function() {
    var id = $(this).data('id');
    var url = $(this).data('url');
    $.ajax({
        url: url,
        method: 'DELETE',
        data: { ids: id },
        success: function(response) {
            // Handle success response
            console.log(response);
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error(xhr.responseText);
        }
    });
});


    </script>
@endsection
