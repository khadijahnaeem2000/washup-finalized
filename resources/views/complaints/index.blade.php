@extends('layouts.master')
@section('title','Complaints')
@section('content')
    <!-- @include( '../sweet_script') -->
    @include( '../sweet_n_datatable_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Lodge @yield('title')</h3>
                    </div>
                     @can('complaint-create')
                        <!-- <div class="card-toolbar">
                            <a  href="{{ route('complaints.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new complaint </a>
                        </div> -->
                    @endcan
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="order_table" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%" >#</th>
                                        <th>Order Id</th>
                                        <th>Customer Name</th>
                                        <th>Contact#</th>
                                        <th>Pickup Date</th>
                                        <th>Delivery Date</th>
                                        <th width="10%" >Lodge</th>
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
    <br>

    <!-- List of lodged complaints -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="complaint_table" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th >#</th>
                                        <th>Order Id</th>
                                        <th>Customer Name</th>
                                        <th>Contact#</th>
                                        <th>Complaint Date</th>
                                        <th>Complaint Status</th>
                                        <th>Tag</th>
                                        <th>Actions</th>
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
          
            // BEGIN::initialization of datatable
            var complaint_table = $('#complaint_table').DataTable({
                "aaSorting": [],
                "paging":   true,
                dom: 'Bfrtip',
                buttons: [
                     'csv'
                ],
                // "info":     false,
                "processing": true,
                "columnDefs": [ {
                "targets": [7],
                    "orderable": false
                } ]
            });
            fetch_complaints();

        });
        function fetch_complaints(){
            $("#complaint_table > tbody").html("");
            var token = $("input[name='_token']").val();
            var cus_url = "{{ route('complaints.index') }}" +'/complaint_list/';
            $.ajax({
                data: "complaint",
                url: cus_url,
                type: "post",
                dataType: 'JSON',
                headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {
                    if(data.details){
                        // console.log(data.details);
                        var rtable = $('#complaint_table').DataTable();
                        rtable.clear().draw();
                        rtable.rows.add($(data.details));
                        rtable.draw();
                    }else{
                        var txt = '';
                        var count = 0 ;
                        $.each(data.error, function() {
                            txt +=data.error[count++];
                            txt +='<br>';
                        });
                        toastr.error(txt);
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

        }

     

     
    </script>
    <script>
        $(document).ready(function () {  
            $('#order_table').DataTable({
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ url('order_for_complaint_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "id"},
                    {"data": "name"}, 
                    {"data": "contact_no"}, 
                    {"data": "pickup_date"},
                    {"data": "delivery_date"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });

           
        });
    </script>
@endsection
