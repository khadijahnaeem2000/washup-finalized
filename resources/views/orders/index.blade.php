@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_n_datatable_script')

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
          
            <div class="card card-custom gutter-b example example-compact search_div">
                 <div class="card-header py-3">
                    {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data','style'=>'width:100%')) !!}
                        <div class="row">
                            
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('from_date','From Pickup Date: ')) !!}
                                    <input type="date" name = "from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('to_date','To Pickup Date: ')) !!}
                                    <input type="date" name = "to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('get_report_btn','Action')) !!}
                                    <a href="" id="get_report_btn" class="btn btn-primary btn-md font-weight-bolder" style="margin-right: 5px; margin-bottom: 5px; width: 100%"><i class="la la-search"></i>Search</a>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="javascript:void(0)" id="div_show" class="btn btn-primary btn-sm ">
                            <i class="la la-search"></i>
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                     <div style="width: 100%; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                    <th width="2%" >#</th>
                                    <th width="5%">
                                    Order#   
                                       </th>
                                    <th>Ref Order#</th>
                                    <th>Has HFQ</th>
                                    <th>Name</th>
                                    <th>Contact#</th>
                                    <th>Pickup Date</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
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
            $('#div_show').click(function (e) {
                $(".search_div").slideToggle();
            });
            // BEGIN::initialization of datatable
            var report_table = $('#myTable').DataTable({
                "aaSorting": [],
                "paging":   true,
                "processing": true,
                // "serverSide": true,
                // "pageLength": 5,
                // dom: 'Bfrtip',
                // buttons: [
                //     //  'csv'
                // ],
                // // "info":     false,
                "columnDefs": [ {
                "targets": [0,1,2,3,4,5,6,7,8,9],
                "orderable": false
                } ]
            });
        });

       

        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // BEGIN:: re-schedule regular orders
            $('#get_report_btn').click(function (e) {
                e.preventDefault();
                $("#myTable > tbody").html("");
                var token = $("input[name='_token']").val();
                var cus_url = "{{ route('orders.index') }}" +'/order_list/';
                $.ajax({
                    data: $('#form').serialize(),
                    url: cus_url,
                   
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.details){
                            // console.log(data.rec);
                            var rtable = $('#myTable').DataTable();
                            rtable.clear().draw();
                            rtable.rows.add($(data.details));
                            rtable.draw();
                        }else{
                            var txt = '';
                            var count = 0 ;
                            var rtable = $('#myTable').DataTable();
                            rtable.clear().draw();
                            $.each(data.error, function() {
                                txt +=data.error[count++];
                                txt +='<br>';
                            });
                            toastr.error(txt);
                        }
                    },
                    error: function (data) {
                        var rtable = $('#myTable').DataTable();
                            rtable.clear().draw();
                        console.log('Error:', data);
                    }
                });

            
            });
            // END:: re-schedule regular orders
        });
    </script>


<!-- <script>
    $(document).ready(function () {  
        var t = $('#myTable').DataTable({
            "aaSorting": [],
                "processing": true,
                "serverSide": false,
                
                "select":true,
                "ajax": "{{ url('order_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "id"},
                    {"data": "ref_order_id"},
                    {"data": "has_hfq"},
                    {"data": "name"},
                    {"data": "contact_no"},
                    {"data": "pickup_date"},
                    {"data": "delivery_date"},
                    {"data": "status_name"},
                    {"data": "action",orderable:false,searchable:false}

                ]
            });
        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
        
    });
</script> -->
@endsection
