@extends('layouts.master')
@section('title','Billing')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <!-- {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data')) !!} -->
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Billing Details</h3>
                        </div>
                        <!-- <form id="form"> -->
                        {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data')) !!}
                            <div class="card-toolbar">
                                    <div style="margin-right: 5px">
                                        {!! Form::select('wash_house_id',$wash_houses, null, array('class' => 'form-control','required'=>'true','id'=>'wash_house_id')) !!}
                                    </div>
                                    <span style="margin-right: 15px; font-weight:bold">Pickup Date</span>  
                                    <div style="margin-right: 5px">
                                        <input type="date" name = "pickup_date" id="pickup_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" required="" autofocus="" />
                                    </div>
                                    <a class="btn btn-primary btn-sm font-weight-bolder" id ="pickup_btn"href="javascript:void(0)" id=""> Search</a>
                            </div>
                        {!! Form::close() !!}
                    </div>
                <!-- {!! Form::close() !!} -->
                <div class="card-body">
                    <h4 id="note" style="color:red; font-weight:bold; text-align:center"></h4>
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive" id="tabl">
                            <h2 style="text-align:center; padding:10px">!!! Please select wash house and pickup date !!! </h2>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>
    <script>
        $('#pickup_btn').click(function (e) {
            e.preventDefault();
            get_data();
        });

        function get_data(){
            $("#tabl").html("");
            var wash_house_id           = document.getElementById('wash_house_id').value; 
            var token                   = $("input[name='_token']").val();
            var cus_url                 = "{{ route('wh_billings.index') }}" +'/wh_order_list/';
            $.ajax({
                data: $('#form').serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                beforeSend:function () {
                    $("#tabl").html("");
                    $('#tabl').append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                },

                success: function (data) {
                    if(data.details){
                        $("#tabl").html("");
                        $('#tabl').append(data.details);
                    }else{
                        $("#tabl").html("");
                        $('#tabl').append("<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2>");
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
               
        $(document).ready(function () {
            var wash_house_id           = document.getElementById('wash_house_id').value;  
            if(wash_house_id > 0){
               
            }else{
                $('#wash_house_id').append('<option disabled>--- No Wash-house ---</option>');
            }
        });
    </script>
@endsection
