@extends('layouts.master')
@section('title','Packing')
@section('content')
    @include( '../sweet_n_datatable_script')
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
        .cus_fnt{
           text-align:center;
           font-weight: bold;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div id= "loaderDiv"><i class="fas fa-spinner fa-spin" style="position:absolute; left:50%; top:50%;font-size:80px; color:#3a7ae0"></i> </div>
            <div class="card card-custom gutter-b example example-compact search_div">
                 <div class="card-header py-3">
                    {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data','style'=>'width:100%')) !!}
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('hub_id','Distribution Hub: ')) !!}
                                    {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id')) !!}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('from_date','From Date: ')) !!}
                                    <input type="date" name = "from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('to_date','To Date: ')) !!}
                                    <input type="date" name = "to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('find_col','Find By: ')) !!}
                                    <select class="form-control"  name = "find_col" style="width: 100%" >
                                        <option value="packing_date">Packing Date</option>
                                        <option value="pickup_date">Pickup Date</option>
                                        <option value="delivery_date">Delivery Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('get_rec_btn','Action')) !!}
                                    <a href="" id="get_rec_btn" class="btn btn-primary btn-md font-weight-bolder" style="margin-right: 5px; margin-bottom: 5px; width: 100%"><i class="la la-search"></i>Get Record</a>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <!-- <div class="card card-custom card-collapsed" data-card="true" > -->
            <div class="card card-custom" >
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Packing Report Summary</h3>
                   </div>
                  
                   <!-- <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                    </div> -->
                </div>
             
                <!-- Showing Addresses -->
                <div class="card-body">
                    <div class="row" style="width:100%;">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 style="font-size: 15px;">Total Orders</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="total_ord">0</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4  style="font-size: 15px;">Totol Pieces</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="total_pcs">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 style="font-size: 15px;">Packed Orders</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="packed_ord">0</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 style="font-size: 15px;">Packed Peices</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="packed_pcs">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 style="font-size: 15px;">Unpacked orders</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="unpacked_ord">0</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 style="font-size: 15px;">Unpacked Pieces</h4>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <span style="font-weight:bold" id="unpacked_pcs">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                   
                </div>
            </div>
            <br>

            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Packing Report</h3>
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
                            <table id="report_table" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="4" class ="cus_fnt">Order</th>
                                        <th colspan="6" class ="cus_fnt">Total Tags</th>
                                        <th colspan="2" class ="cus_fnt">Weight (Kg)</th>
                                        <th class ="cus_fnt">Polybags</th>
                                        <th colspan="2" class ="cus_fnt">Route Info</th>
                                        <th colspan="3" class ="cus_fnt">Packing</th>
                                        <th colspan="3" class ="cus_fnt">Date of</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Washhouse</th>
                                        <th>Pack</th>
                                        <th>Tagged</th>
                                        <th>Scan</th>
                                        <th>N.R</th>
                                        <th>Broken</th>
                                        <th>Packed</th>
                                        <th>HFQ</th>
                                        <th>Pick</th>
                                        <th>Packed</th>
                                        <th>Issued</th>
                                        <th>D.Rider</th>
                                        <th>D.Route</th>
                                        <th width="8%">Packer</th>
                                        <th >Packing_Date</th>
                                        <th>Time</th>
                                        <th>Pickup_Date</th>
                                        <th >Time</th>
                                        <th>Delivery_Date</th>
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
        $(".search_div").hide();
        $(document).ready(function () { 
 
            $('#div_show').click(function (e) {
                $(".search_div").slideToggle();
            });
        // BEGIN::initialization of datatable
            var report_table = $('#report_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                dom: 'Bfrtip',
                buttons: [
                     'csv'
                ],
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [0,1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,18,20],
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
            $('#get_rec_btn').click(function (e) {
                e.preventDefault();
                $("#loaderDiv").show();
                $("#report_table > tbody").html("");
                var token = $("input[name='_token']").val();
                var cus_url = "{{ route('packings.index') }}" +'/pack_list/';
                $.ajax({
                    data: $('#form').serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                       
                        if(data.data){
                            var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                            rtable.rows.add($(data.details));
                            rtable.draw();


                            $('#total_ord').html(data.summary['total_ord']);
                            $('#total_pcs').html(data.summary['total_pcs']);
                            $('#packed_ord').html(data.summary['packed_ord']);
                            $('#packed_pcs').html(data.summary['packed_pcs']);
                            $('#unpacked_ord').html(data.summary['unpacked_ord']);
                            $('#unpacked_pcs').html(data.summary['unpacked_pcs']);
                            $("#loaderDiv").hide();
                        }else{
                            $("#loaderDiv").hide();
                            var txt = '';
                            var count = 0 ;
                            var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                            $.each(data.error, function() {
                                txt +=data.error[count++];
                                txt +='<br>';
                            });
                            toastr.error(txt);
                        }
                        
                    },
                    error: function (data) {
                        $("#loaderDiv").hide();
                        var rtable = $('#report_table').DataTable();
                        rtable.clear().draw();
                        toastr.error("Something went wrong!!!!");
                        // console.log('Error:', data);
                    }
                });

            
            });
            // END:: re-schedule regular orders
        });
    </script>
@endsection
