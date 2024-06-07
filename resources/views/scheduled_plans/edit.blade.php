@extends('layouts.master')

@section('title','Scheduled Route Plan')

@section('content')



@include( '../sweet_script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js" defer></script> 

<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js" defer></script>  -->

<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet"> -->

<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet" defer>    -->

<style type="text/css">

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

    {!! Form::open(array('id'=>'rider_plan_form','enctype'=>'multipart/form-data')) !!}

        <div class="row">

            <div class="col-lg-12">

                <!--begin::Card-->

                <div class="card card-custom gutter-b example example-compact">

                

                        <div class="card-header">

                            <h3 class="card-title">Route Plan (



                                                            @if(isset($rider->name))

                                                                {{$rider->name}}

                                                            @else

                                                                ---



                                                            @endif

                                                                

                                                                

                                                        )</h3>

                            <div class="card-toolbar">

                                <span style="margin-right: 10px; font-weight:bold">Riders</span>  

                                <div style="margin-right: 10px">

                                    {{ Form::hidden('rider_id', $id, array('id'=>'rider_id','class' => 'form-control')) }}

                                    {{ Form::hidden('dt', $dt, array('id'=>'dt','class' => 'form-control')) }}

                                    {!! Form::select('riders',$riders, $id, array('class' => 'form-control','required'=>'true','id'=>'riders')) !!}

                                </div>

                                    

                                

                                <!-- Button trigger Pickup Order modal-->

                                <a class="btn btn-primary btn-sm font-weight-bolder" id ="btn_update_rider_plan" href="javascript:void(0)" id=""> Update</a>



                            </div>

                        </div>

                        <!--begin::Form-->

                



                        <div class="card-body">

                            <div class="row" id="rider_plan_table">



                            </div>

                        </div>

                        

                    

                    <!--end::Form-->

                </div>

                <!--end::Card-->

            </div>

        </div>

    {!! Form::close() !!}

    <script type="text/javascript">



        $(document).ready(function () { 

            $(function () {

                // Ajax request setup

                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }

                });

            });

           

            re_draw_all();

        });





    



        $('#btn_update_rider_plan').click(function (e) {

            e.preventDefault();

            var cus_url = "{{ route('scheduled_plans.index') }}" +'/update_rider_plan/';

            var form_id = '#rider_plan_form';

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



        function re_draw_all(){

            var rider_id    = $("#rider_id").val();

            var dt    = $("#dt").val();

            tbl             = 'rider_plan_table';

            fn              = 'fetch_rider_plan';

            fn_redraw_table(rider_id,tbl,dt);

        }

        function fn_redraw_table(rider_id,tbl,dt){

            console.log(dt);

            var fn = "{{ route('scheduled_plans.index') }}" +'/fetch_rider_plan/';

            $("#"+tbl).html("");

            var token  = $("input[name='_token']").val();

            $.ajax({

                url: fn,

                method: 'POST',

                data: {rider_id:rider_id,dt: dt, _token:token},

                // dataType: 'json',

                beforeSend:function () {

                    $("#"+tbl).html("");

                    $("#"+tbl).append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");

                },

                success: function (data) {

                    if(data.details){
                        console.log(data.details);

                        $("#"+tbl).html("");

                        $("#"+tbl).html(data.details);

                    }else{

                        

                        $("#"+tbl).html("");

                        $("#"+tbl).append("<h2 style='text-align:center; padding:10px;margin: 0px auto;'>!!! No Record Found !!! </h2>");

                    }

                },

                error: function (data) {

                    console.log('Error:', data);

                }

            });

           

        }



    </script>

@endsection

