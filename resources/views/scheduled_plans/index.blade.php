@extends('layouts.master')
@section('title','Schedule Route Plan')
@section('content')
<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>
<script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
<script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script>

 <!-- @include( '../sweet_script') -->
    <style type="text/css">
    
        #loaderDiv{
            width:100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.2);
            z-index:9999;
            display:none;
        }
        .data_center{
            vertical-align: middle;
            text-align: center;
        }
        #plan_table th{
        vertical-align: middle;
            text-align: center;
        }
    
    .table th, .table td{
        vertical-align: middle;
            text-align: center;
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
    <!-- BEGIN::Route Plan -->
    <div class="row">
        
        <div class="col-lg-12">
            <div id= "loaderDiv"><i class="fas fa-spinner fa-spin" style="position:absolute; left:50%; top:50%;font-size:80px; color:#3a7ae0"></i> </div>
            <!--begin::Card-->
            {!! Form::open(array('id'=>'scheduled_plan_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Route Plan</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <span style="margin-right: 10px; font-weight:bold"> Plan For </span>  
                            <div style="margin-right: 10px">
                                {!! Form::date('dt', date('Y-m-d'), array('class' => 'form-control','id'=>'dt')) !!}
                            </div>

                            <span style="margin-right: 10px; font-weight:bold">Distribution Hub</span>  
                            <div style="margin-right: 10px">
                                {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id')) !!}
                            </div>
                            
                        
                            <!-- Button trigger Pickup Order modal-->
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="plan_btn" href="javascript:void(0)" id=""> Search</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="scheduled_plan_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div><br>
    <!-- END::Route Plan -->

    <!-- BEGIN:: Payment Only Order -->
    <div class="row">
        
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'payment_order_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Payment Only Order</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <a class="btn btn-danger btn-sm font-weight-bolder mr-1" id ="btn_cancel_payment_rides"href="javascript:void(0)" id=""> Cancel Ride(s)</a>
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="btn_schedule_payment_rides"href="javascript:void(0)" id=""> Schedule Route</a>
                            

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="payment_order_table">
                            
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
                            <h3 class="card-label">Plan Same day order<span style="width: 100%;display: inline-block;font-size: 12px;font-weight: 400;">This section has only today's orders</span></h3>
                        </div>
                        <div class="card-toolbar">

                           <!-- <span style="margin-right: 15px; font-weight:bold">Plan for</span>   -->
                           <div class="input-icon" style="margin-right: 5px">
                                <input type="hidden" name = "plan_date" id="plan_date" value=""  class="form-control btn-sm" />
                            </div>

                            <!--begin::Button-->
                            <a class="btn btn-success btn-sm font-weight-bolder" id ="btn_schedule_reg_orders"href="javascript:void(0)" id=""> Schedule Route</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="orders_table">
                            <!-- <h4 style='text-align:center; padding:10px'> Please press search button  </h4> -->
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
            // console.log("clicked");
            re_draw_all();
               
        });

        $('#btn_cancel_payment_rides').click(function (e) {
            e.preventDefault();
            var boxes = $('.payment_id').is(':checked');
            if(!(boxes)){
                Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select Payment only rides!',
                // footer: '<a href>Why do I have this issue?</a>'
                })
            }else {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $("#loaderDiv").show();
                        var cus_url = "{{ route('scheduled_plans.index') }}" +'/cancel_payment_rides/';
                        var form_id = '#payment_order_form';
                        $.ajax({
                            data: $(form_id).serialize(),
                            url: cus_url,
                            type: "POST",
                            dataType: 'json',
                            success: function (data) {
                                if(data.success){
                                    re_draw_all();
                                    toastr.success(data.success);
                                    $("#loaderDiv").hide();
                                }else{
                                    var txt     = '';
                                    var count   = 0 ;
                                    $.each(data.error, function() {
                                        txt     += data.error[count++];
                                        txt     +='<br>';
                                    });
                                    
                                    $("#loaderDiv").hide();
                                    toastr.error(txt);
                                }
                            },
                            error: function (data) {
                                
                                $("#loaderDiv").hide();
                                toastr.error("Something went wrong!!!");
                                // console.log('Error:', data);
                            }
                        });

                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            'Your imaginary data is safe :)',
                            'error'
                        )
                    }
                })

            }
        });

        
        $('#btn_schedule_payment_rides').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('scheduled_plans.index') }}" +'/schedule_payment_rides/';
            var form_id = '#payment_order_form';
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
                        var txt     = '';
                        var count   = 0 ;
                        $.each(data.error, function() {
                            txt     += data.error[count++];
                            txt     +='<br>';
                        });
                        toastr.error(txt);
                    }
                },
                error: function (data) {
                    toastr.error("Something went wrong!!!");
                    // console.log('Error:', data);
                }
            });
        });

        $('#btn_schedule_reg_orders').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('scheduled_plans.index') }}" +'/schedule_reg_orders/';
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
                        var txt     = '';
                        var count   = 0 ;
                        $.each(data.error, function() {
                            txt     += data.error[count++];
                            txt     += '<br>';
                        });
                        toastr.error(txt);
                    }
                },
                error: function (data) {
                    toastr.error("Something went wrong!!!");
                    console.log('Error:', data);
                }
            });
        });

        function re_draw_all(){
            var tbl         = '';
            var fn          ='';
            var hub_id      = document.getElementById('hub_id').value; 
            var dt          = document.getElementById('dt').value; 
                   
            $('#plan_date').val(dt);
            tbl             = 'scheduled_plan_table';
            fn              = 'fetch_scheduled_plan';
            fn_redraw_table(hub_id,dt,tbl,fn);

            tbl             = 'payment_order_table';
            fn              = 'fetch_payment_order';
            fn_redraw_table(hub_id,dt,tbl,fn);
           
            tbl             = 'orders_table';
            fn              = 'fetch_schedule_orders';
            fn_redraw_table(hub_id,dt,tbl,fn);
        }
    
        function fn_redraw_table(hub_id,dt,tbl,fn){
            $("#"+tbl).html("");
            var token  = $("input[name='_token']").val();
            $.ajax({
                url: fn,
                method: 'POST',
                data: {hub_id:hub_id,dt:dt, _token:token},
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
                        $("#"+tbl).append("<h2 style='text-align:center; padding:10px;margin: 0px auto;'>!!! No Record Found !!! </h2>");
                    }
                },
                error: function (data) {
                    toastr.error("Something went wrong!!!");
                    // console.log('Error:', data);
                }
            });
           
        }
        
    </script>

    
   

@endsection
