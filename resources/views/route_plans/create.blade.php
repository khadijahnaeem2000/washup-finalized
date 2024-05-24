@extends('layouts.master')
@section('title','CSR Dashboard')
@section('content')
<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>
<script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
<script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script>

 <!-- @include( '../sweet_script') -->
    <style type="text/css">
        .data_center{
            vertical-align: middle;
            text-align: center;
        }
        #rider_table th{
            text-align: center;
            vertical-align: middle;
        }
        #order_table th, #order_table td{
            text-align: center;
            vertical-align: middle;
        }
    .tRed{
        color:red;
        background-color:#ebe6c7;
        font-weight: bold;
        padding:2px;
        border-radius:2px;
        display:block;
        margin:2px;
    }
    .tGreen{
        color:green;
        background-color:#ebe6c7;
        font-weight: bold;
        padding:2px;
        border-radius:2px;
        display:block;
        margin:2px;
    }

    .tBlue{
        color:blue;
        background-color:#ebe6c7;
        font-weight: bold;
        padding:2px;
        border-radius:2px;
        display:block;
        margin:2px;
    }
	</style>
    <!-- BEGIN::Summary -->
    <div class="row">
        
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'summary_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Summsssary</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->

                            <span style="margin-right: 15px; font-weight:bold">Distribution Hub</span>  
                            <div class="input-icon" style="margin-right: 5px">
                                {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id')) !!}
                            </div>
                        
                            <!-- Button trigger Pickup Order modal-->
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="plan_btn"href="javascript:void(0)" id=""> Search</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="summary_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div><br>
    <!-- END::Summary -->

    <!-- BEGIN:: Rider Profile -->
    <div class="row">
        
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'riders_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Riders' Profile</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="btn_update_riders"href="javascript:void(0)" id=""> Update Riders</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="riders_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div><br>
    <!-- END:: Rider Profile -->

    <!-- BEGIN:: Orders -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'orders_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Manage Route Plan</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <a class="btn btn-success btn-sm font-weight-bolder" id ="btn_store_resort"href="javascript:void(0)" id=""> Save and Resort</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="orders_table">
                            <h4 style='text-align:center; padding:10px'> Please press search button  </h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <a class="btn btn-primary mr-2" id ="btn_get_route"href="javascript:void(0)" id=""> Get Route & Save</a>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <!-- END:: Orders -->



    <!-- BEGIN::AJAX CRUD Order -->
    <script type="text/javascript">
        $(document).ready(function () { 

           
           
        });

        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
         
            
        });
    </script>
    <!-- END::AJAX CRUD Order -->

    
    <!-- re-draw-order table -->
    <script type="text/javascript">
        $('#plan_btn').click(function (e) {
            e.preventDefault();
            console.log("clicked");
            re_draw_all();
               
        });

        
        $('#btn_store_resort').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('route_plans.index') }}" +'/store_resort/';
            var form_id = '#orders_form';
            $.ajax({
                data: $(form_id).serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        re_draw_all();
                        toastr.success(data.success);
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
        });



        $('#btn_update_riders').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('route_plans.index') }}" +'/update_riders/';
            var form_id = '#riders_form';
            $.ajax({
                data: $('#riders_form').serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        re_draw_all();
                        // location.reload(); // reload the page
                        toastr.success(data.success);
                        // order_table.draw();
                    }else{
                        var txt = '';
                        var count = 0 ;
                        $.each(data.error, function() {
                            txt +=data.error[count++];
                            txt +='<br>';
                        });
                        toastr.error(txt);
                    }
                    // table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });

        

        function re_draw_all(){
            var tbl         = '';
            var fn          ='';
            var hub_id      = document.getElementById('hub_id').value; 
                   
            tbl             = 'summary_table';
            fn              = 'fetch_route_summary';
            fn_redraw_table(hub_id,tbl,fn);

            tbl             = 'riders_table';
            fn              = 'fetch_route_riders';
            fn_redraw_table(hub_id,tbl,fn);

            tbl             = 'orders_table';
            fn              = 'fetch_route_orders';
            fn_redraw_table(hub_id,tbl,fn);
        }

       
    
        function fn_redraw_table(hub_id,tbl,fn){
            $("#"+tbl).html("");
            var token  = $("input[name='_token']").val();
            $.ajax({
                url: fn,
                method: 'POST',
                data: {hub_id:hub_id, _token:token},
                // dataType: 'json',
                beforeSend:function () {
                    $("#"+tbl).html("");
                    $("#"+tbl).append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                },

                success: function (data) {
                    if(data.details){
                        // console.log(data.details);
                        $("#"+tbl).html("");
                        $("#"+tbl).html(data.details);
                    }else{
                        $("#"+tbl).html("");
                        $("#"+tbl).append("<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2>");
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
           
        }
        
    </script>
    <!-- END::fetching customer detail by contact no -->

   
@endsection
