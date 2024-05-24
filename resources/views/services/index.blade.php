@extends('layouts.master')
@section('title','Services')
@section('content')

@include( '../sweet_script')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom">
            <div class="card-header py-3">
                <div class="card-title">
                    <h3 class="card-label">Manage @yield('title')</h3>
                </div>
                @can('service-create')
                <div class="card-toolbar">
                    <a href="{{ route('services.create') }}" class="btn btn-primary font-weight-bolder">
                        <i class="la la-plus"></i>Add new service
                    </a>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div style="width: 100%; padding-left: -10px; ">
                    <div class="table-responsive">
                        <div id="draggableTable">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%"></th>
                                        <th  >#</th>
                                        <th>Name</th>
                                        <th>Rate</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js" defer></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet" defer>

<!-- Your HTML and Blade code remains unchanged -->

<script>
    $(document).ready(function () {
        var table;

        function initializeDataTable() {
            if (table) {
                // If DataTable is already initialized, destroy it first
                table.destroy();
            }

            table = $('#myTable').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": false,
                "ajax": {
                    url: "{{ url('service_list') }}",
                    type: "GET",
                    dataSrc: 'data'
                },
                "columns": [
                    { "data": "id",
                        "render":function(data){
                            return '<span hidden>'+data+'</span>';
                        }
                    },
                     { "data": "orderNumber" },
                    { "data": "name" },
                    { "data": "rate" },
                    { "data": "unit_name" },
                   
                    {
                        "data": "status",
                        "render": function (data) {
                            var checked = data == 1 ? 'checked' : '';
                            return '<span class="switch switch-outline switch-icon switch-primary">' +
                                '<label>' +
                                '<input type="checkbox" class="form-control" data-status="' + data + '" ' + checked + '>' +
                                '<span></span>' +
                                '</label>' +
                                '</span>';
                        }
                    },
                    { 
                        "data": "action", 
                        "orderable": true, 
                        "searchable": false 
                    },
                   
                ],
                "order": [[1, 'asc']],
              
            });

            // Initialize draggable functionality
            $("#myTable tbody").sortable({
                update: function(event, ui) {
                    var serviceOrders = []; // Array to hold service IDs and order numbers
                    $(this).children().each(function(index) {
                        var serviceId = $(this).find('td:nth-child(1)').text(); // Get service ID from first column
                        var orderNumber = index + 1;
                        serviceOrders.push({ serviceId: serviceId, orderNumber: orderNumber });
                    });
                    updateOrderNumbers(serviceOrders);
                }
            }).disableSelection();
        }

        function updateOrderNumbers(serviceOrders)
            {
    // Perform AJAX request to update order numbers
    var token = $("input[name='_token']").val();
    $.ajax({
        url: "{{ route('update_order_number') }}",
        method: 'POST',
        data: { serviceOrders: serviceOrders, _token: token },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                toastr.success(data.success); // Display success message
                
                // Set a timeout to refresh the page after 30 seconds
                setTimeout(function() {
                    location.reload(); // Refresh the page
                }, 1000); // 30000 milliseconds = 30 seconds
            } else {
                toastr.error(data.error);
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}


        initializeDataTable();
    });
</script>

<!-- Your HTML and Blade code remains unchanged -->



</script>

@endsection
