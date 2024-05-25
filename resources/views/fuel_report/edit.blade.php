@extends('layouts.master')

@section('title','Fuel Reports')

@section('content')

    @include( '../sweet_n_datatable_script')

    <style type="text/css">

       #order_table th,#order_table td{

        vertical-align: middle;

            text-align: center;

        }

        #order_table th{

            font-weight:bold;

        }

        .img_cls{

            display: block;

            margin-left: auto;

            margin-right: auto;

            width: 150px;

            height: 150px

        }

    </style>



    <div class="row">

        <div class="col-lg-12">

            <!--begin::Card-->

            <div class="card card-custom" data-card="true" >

                <div class="card-header py-3">

                    <div class="card-title">

                        <h3 class="card-label">

                          Reports - Summary

                        </h3>

                    </div>

                    <div class="card-toolbar">

                        <!-- <span></span>

                        <a href="#" class="btn btn-success font-weight-bolder">

                        <i class="fas fa-file-csv"></i>Export XLSX</a><span></span> -->

                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">

                            <i class="ki ki-arrow-down icon-nm"></i>

                        </button>

                    </div>

                </div>

                <!--begin::Form-->

                <div class="card-body">

                    <div style="width: 100%; padding-left: -10px; ">

                        <div class="table-responsive">

                            <table id="myTable" class="table table-bordered" style="width: 100%;" cellspacing="0">

                                <tr>

                                    <th style="text-align:center">Total  </th>

                                    <th style="text-align:center">Amount</th>

                                    <th style="text-align:center">Total  </th>

                                    <th style="text-align:center">Amount</th>

                                </tr>

                                <tr>

                                    <td style="text-align:center">Regular Order(s) Bill  </td>

                                    <th style="text-align:center">{{$tot_order_bill_amnt}}</th>

                                    <td style="text-align:center">Regular Order(s) Received  </td>

                                    <th style="text-align:center">{{$tot_order_recd_amnt}}</th>

                                </tr>

                                <tr>

                                    <td style="text-align:center">Payment Only Order(s) Bill  </td>

                                    <th style="text-align:center">{{$tot_ride_bill_amnt}}</th>

                                    <td style="text-align:center">Payment Only Order(s) Received  </td>

                                    <th style="text-align:center">{{$tot_ride_recd_amnt}}</th>

                                </tr>

                                <tr>

                                    <th style="text-align:center">Total Amount </th>

                                    <th style="text-align:center">{{ ($tot_ride_bill_amnt + $tot_order_bill_amnt)}}</th>

                                    <th style="text-align:center">Total Received  Amount</th>

                                    <th style="text-align:center">{{ ($tot_ride_recd_amnt + $tot_order_recd_amnt) }}</th>

                                </tr>

                            </table>

                        

                        </div>

                    </div>

                </div>

                <!--end::Form-->

            </div>

            <!--end: Datatable-->

        </div>

    </div>

    <br>

    <div class="row">

        <div class="col-lg-12">

            <!--begin::Card-->

            <div class="card card-custom" data-card="true" >

                <div class="card-header py-3">

                    <div class="card-title">

                        <h3 class="card-label">

                          Reports

                        </h3>

                    </div>

                    <div class="card-toolbar">

                        <!-- <span></span>

                        <a href="#" class="btn btn-success font-weight-bolder">

                        <i class="fas fa-file-csv"></i>Export XLSX</a><span></span> -->

                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">

                            <i class="ki ki-arrow-down icon-nm"></i>

                        </button>

                    </div>

                </div>

                <!--begin::Form-->

                <div class="card-body">

                    <div style="width: 100%; padding-left: -10px; ">

                        <div class="table-responsive">

                            <table id="myTable" class="table table-bordered" style="width: 100%;" cellspacing="0">

                                <tr>

                                    <td colspan="2">

                                        <h3 style="text-align:center">Rider -  

                                            @if(isset($record->rider_name))

                                                {{$record->rider_name}} 

                                            @endif

                                            @if(isset($record->vehicle_name))

                                                ({{$record->vehicle_name}})

                                            @endif

                                        </h3>

                                    </td>

                                </tr>

                                <tr>

                                  <td>
    @if(isset($record->start_img))
        <img src="{{ config('app.base_url') }}/uploads/meters/{{ $record->start_img }}" class="img_cls">
    @else
        <img src="{{ config('app.base_url') }}/uploads/users/no_image.png" class="img_cls">
    @endif
</td>
  
<td>
    @if(isset($record->end_img))
        <img src="{{ config('app.base_url') }}/uploads/meters/{{ $record->end_img }}" class="img_cls">
    @else
        <img src="{{ config('app.base_url') }}/uploads/users/no_image.png" class="img_cls">
    @endif
</td>


                                    </td>

                                </tr>

                                <tr>

                                    <th style="text-align:center">Start Ride</th>
                                  

                                    <th style="text-align:center">End Ride</th>
                              

                                </tr>
                                 <tr data-record-id="{{$record->id}}">
                                    <td class="start-reading" style="text-align: center;">{{$record->start_reading}}</td>
                                    <td class="end-reading" style="text-align: center;">{{$record->end_reading}}</td>
                                </tr>
                                    @if($record->updated_by != null)
                          
                                <tr>
                                    @if($record->old_start_reading != null && $record->old_end_reading == null)
                                    <td class="old-start-reading" style="text-align: center;">Old Reading :{{$record->old_start_reading}}</td>
                                     <td class="old-end-reading" style="text-align: center;"></td>
                                    @endif
                                    @if($record->old_start_reading == null &&$record->old_end_reading != null )
                                    <td class="old-start-reading" style="text-align: center;"></td>
                                     <td class="old-end-reading" style="text-align: center;">Old Reading :{{$record->old_end_reading}}</td>
                                     @endif
                                     @if($record->old_start_reading != null && $record->old_end_reading != null)
                                     <td class="old-start-reading" style="text-align: center;">Old Reading :{{$record->old_start_reading}}</td>
                                     <td class="old-end-reading" style="text-align: center;">Old Reading :{{$record->old_end_reading}}</td>
                                     @endif
                                </tr>
                                <tr>
                                    @if($record->old_start_reading != null && $record->old_end_reading == null)
                                <td class="new-start-reading" style="text-align: center;">
                                    
                                    <span class="user" style="display: inline-block; padding:5px;width: 20%;text-align: center; height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->UpdateName}}</span>
                                    <span  class="date"style="display: inline-block;padding:5px;width: 20%;text-align: center; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span>
                                </td>
                                <td class="new-end-reading" style="text-align: center;">
                                    
                                    <span class="user" ></span>
                                    <span class="date"></span>
                                </td>
                                @endif
                                @if($record->old_end_reading != null && $record->old_start_reading == null)
                                <td class="new-start-reading" style="text-align: center;">
                                    
                                    <span class="user" ></span>
                                    <span  class="date"></span>
                                </td>
                                     <td class="new-end-reading" style="text-align: center;">
                                    
                                    <span class="user" style="display: inline-block; padding:5px;width: 20%; height: 40%;text-align: center; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->UpdateName}}</span>
                                    <span class="date"style="display: inline-block;padding:5px;width: 20%; height: 50%; text-align: center;background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span>
                                </td>
                                    @endif

                                @if($record->old_start_reading != null && $record->old_end_reading != null)
                                <td class="new-start-reading" style="text-align: center;">
                                    
                                    <span class="user" style="display: inline-block; padding:5px;width: 20%;text-align: center; height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->UpdateName}}</span>
                                    <span  class="date"style="display: inline-block;padding:5px;width: 20%;text-align: center; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span>
                                </td>
                                  <td class="new-end-reading" style="text-align: center;">
                                    
                                    <span class="user" style="display: inline-block; padding:5px;width: 20%; height: 40%;text-align: center; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->UpdateName}}</span>
                                    <span class="date"style="display: inline-block;padding:5px;width: 20%; height: 50%; text-align: center;background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span>
                                </td>
                                @endif
                                </tr>
                                @endif

                            </table>

                        

                        </div>

                    </div>

                </div>

                <!--end::Form-->

            </div>

            <!--end: Datatable-->

        </div>

    </div>

    <br>



  



    <div class="row">

        <div class="col-lg-12">

            <!--begin::Card-->

            <div class="card card-custom" data-card="true" >

                <div class="card-header py-3">

                    <div class="card-title">

                        <h3 class="card-label">

                            General Report -  

                            @if(isset($record->rider_name))

                                {{$record->rider_name}} 

                            @endif

                        </h3>

                    </div>

                    <div class="card-toolbar">

                        

                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">

                            <i class="ki ki-arrow-down icon-nm"></i>

                        </button>

                    </div>

                </div>

                <!--begin::Form-->

                <div class="card-body">

                    <div style="width: 100%; padding-left: -10px; ">

                        <div class="table-responsive">

                            <table id="order_table" class="table table-bordered" style="width: 100%;" cellspacing="0">

                                <thead>

                                    <tr>

                                        <th ></th>

                                        <th colspan="2"> Order</th>

                                        <th colspan="3"> Customer</th>

                                        

                                        <th colspan="2">Payment</th>



                                        <th colspan="2">Polybags</th>

                                        <th colspan="2">Timeslot</th>

                                    </tr>

                                    <tr>

                                        <th>Sr#</th>



                                        <th>#</th>

                                        <th>Action</th>



                                        <th>Name</th>

                                        <th>Phone#</th>

                                        <th>Address</th>

                                        

                                        <th>Bill</th>

                                        <th>Received </th>

                                        

                                        <th>Allotted </th>

                                        <th>Scanned</th>

                                        <th>Allotted</th>

                                        <th title="Highlight row, whose time < or > alloted time">Executed</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $i = 0;

                                        if(isset($orders)){ $tot_bill = 0; $tot_received_amount = 0;$tot_allotted_bags = 0;$tot_scanned_bags = 0;

                                            foreach($orders as $key =>$value){ $i++; ?>

                                                <?php 

                                                    $time_at_loc = (strtotime($value->time_at_loc));

                                                    $start_tm    = (strtotime($value->start_time));

                                                    $end_tm      = (strtotime($value->end_time));

                                                    

                                                    if( ($time_at_loc >= $start_tm) && ($time_at_loc <= $end_tm) ){

                                                        $tr_color = "";

                                                    }else{

                                                        if( (!(isset($value->time_at_loc)))  && ($value->time_at_loc) == null ){

                                                            $tr_color = "";

                                                        }else{

                                                            $tr_color = "YELLOW";

                                                        }

                                                       

                                                    }

                                                ?>

                                                <tr style="background-color: {{$tr_color}};">

                                                    <td>

                                                        {{$i}}

                                                    </td>

                                                    <td>

                                                        @if(isset($value->order_id))

                                                            {{$value->order_id}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->status_name))

                                                            {{$value->status_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->customer_name))

                                                            {{$value->customer_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->customer_contact_no))

                                                            {{$value->customer_contact_no}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(($value->status_id) == 1)

                                                            @if(isset($value->pickup_address))

                                                                {{$value->pickup_address}}

                                                            @endif

                                                        @else

                                                            @if(isset($value->delivery_address))

                                                                {{$value->delivery_address}}

                                                            @endif



                                                        @endif

                                                    </td>

                                                    @if( (isset($value->received_amount)) && (isset($value->bill))  )

                                                        @if( (($value->received_amount) != ($value->bill) ) && (($value->status_name) != 'Pickup'))

                                                            <?php $td_color = "RED"; $font_color = "WHITE"; ?>

                                                        @else

                                                            <?php $td_color = ""; $font_color = ""; ?>

                                                        @endif

                                                    @endif

                                                    <td style="background-color: {{$td_color}}; color:{{$font_color}}">

                                                        @if((isset($value->bill) )&& (($value->status_name) != 'Pickup'))

                                                            {{$value->bill}}

                                                            <?php $tot_bill+=($value->bill) ?>

                                                        @endif

                                                    </td>

                                                

                                                        <td style="background-color: {{$td_color}}; color:{{$font_color}}">

                                                            @if((isset($value->received_amount) )&& (($value->status_name) != 'Pickup'))

                                                                {{$value->received_amount}}

                                                            <?php $tot_received_amount+=($value->received_amount) ?>

                                                            @endif

                                                        </td>

                                                

                                                    <td>

                                                        @if( (isset($value->allotted_bags)) && (($value->status_name) != 'Pickup') )

                                                            {{$value->allotted_bags}}

                                                            <?php $tot_allotted_bags+=($value->allotted_bags) ?>

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if( (isset($value->scanned_bags))  && (($value->status_name) != 'Pickup'))

                                                            {{$value->scanned_bags}}

                                                            <?php $tot_scanned_bags+=($value->scanned_bags) ?>

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->timeslot_name))

                                                            {{$value->timeslot_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->time_at_loc))

                                                            {{$value->time_at_loc}}

                                                        @endif

                                                    </td>

                                                </tr><?php

                                            }

                                        }

                                    ?>

                                    <tr style="background-color:#ededed">

                                        <td></td><td></td><td></td><td></td><td></td>

                                        <th>Total</th>

                                        <th>{{$tot_bill}}</th>

                                        <th>{{$tot_received_amount}}</th>

                                        <th >{{$tot_allotted_bags}}</th>

                                        <th >{{$tot_scanned_bags}}</th>

                                        <td></td><td></td>

                                    </tr>

                                </tbody>

                              



                            </table>

                        </div>

                    </div>

                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-lg-12 text-right">

                            <!-- <a href="http://app.washup.com.pk/cal_dis_time" class="btn btn-primary mr-2">Get Route & Save</a> -->

                        </div>

                    </div>

                </div>

                <!--end::Form-->

            </div>

            <!--end: Datatable-->

        </div>

    </div>

    <br>

    <div class="row">

        <div class="col-lg-12">

            <!--begin::Card-->

            <div class="card card-custom" data-card="true" >

                <div class="card-header py-3">

                    <div class="card-title">

                        <h3 class="card-label">

                           Payment Only Orders

                        </h3>

                    </div>

                    <div class="card-toolbar">

                        

                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">

                            <i class="ki ki-arrow-down icon-nm"></i>

                        </button>

                    </div>

                </div>

                <!--begin::Form-->

                <div class="card-body">

                    <div style="width: 100%; padding-left: -10px; ">

                        <div class="table-responsive">

                            <table id="rides_table" class="table table-bordered" style="width: 100%;" cellspacing="0">

                                <thead>

                                    <tr>

                                        <th ></th>

                                        <th colspan="2"> Order</th>

                                        <th colspan="3"> Customer</th>

                                        <th colspan="2">Payments</th>

                                        <th colspan="2">Timeslot</th>

                                    </tr>

                                    <tr>

                                        <th>Sr#</th>

                                        <th>#</th>

                                        <th>Action</th>



                                        <th>Name</th>

                                        <th>Phone#</th>

                                        <th>Address</th>

                                        

                                        <th>Bill</th>

                                        <th>Received </th>

                                        

                                        <th>Allotted</th>

                                        <th>Executed</th>

                                    </tr>



                                </thead>

                                <tbody>

                                    <?php $i = 0;

                                        if(isset($rides)){ $tot_bill = 0; $tot_received_amount = 0;

                                            foreach($rides as $key =>$value){ $i++; ?>

                                                <?php 

                                                    $time_at_loc = (strtotime($value->time_at_loc));

                                                    $start_tm    = (strtotime($value->start_time));

                                                    $end_tm      = (strtotime($value->end_time));

                                                    if( ($time_at_loc >= $start_tm) && ($time_at_loc <= $end_tm) ){

                                                        $tr_color = "";

                                                    }else{

                                                        if( (!(isset($value->time_at_loc)))  && ($value->time_at_loc) == null ){

                                                            $tr_color = "";

                                                        }else{

                                                            $tr_color = "YELLOW";

                                                        }

                                                       

                                                    }

                                                ?>

                                                <tr style="background-color: {{$tr_color}};">

                                                    <td>

                                                        {{$i}}

                                                    </td>

                                                    <td>

                                                        @if(isset($value->id))

                                                            {{$value->id}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->status_name))

                                                            {{$value->status_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->customer_name))

                                                            {{$value->customer_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->customer_contact_no))

                                                            {{$value->customer_contact_no}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->customer_address))

                                                            {{$value->customer_address}}

                                                        @endif

                                                    </td>

                                                    @if( (isset($value->received_amount)) && (isset($value->bill)))

                                                        @if( ($value->received_amount) != ($value->bill) )

                                                            <?php $td_color = "RED"; $font_color = "WHITE"; ?>

                                                        @else

                                                            <?php $td_color = ""; $font_color = ""; ?>

                                                        @endif

                                                    @endif

                                                    <td style="background-color: {{$td_color}}; color:{{$font_color}}">

                                                        @if(isset($value->bill))

                                                        <?php if($value->bill <0 ) {

                                                                $value->bill = - ($value->bill);

                                                            }

                                                        ?>

                                                            {{$value->bill}}

                                                            <?php $tot_bill+=($value->bill) ?>

                                                        @endif

                                                    </td>

                                                

                                                    <td style="background-color: {{$td_color}}; color:{{$font_color}}">

                                                        @if(isset($value->received_amount))

                                                            {{$value->received_amount}}

                                                        <?php $tot_received_amount+=($value->received_amount) ?>

                                                        @endif

                                                    </td>

                                                   

                                                    <td>

                                                        @if(isset($value->timeslot_name))

                                                            {{$value->timeslot_name}}

                                                        @endif

                                                    </td>

                                                    <td>

                                                        @if(isset($value->time_at_loc))

                                                            {{$value->time_at_loc}}

                                                        @endif

                                                    </td>

                                                </tr><?php

                                            }

                                        }

                                    ?>

                                    <tr style="background-color:#ededed">

                                        <td></td><td></td><td></td><td></td><td></td>

                                        <th>Total</th>

                                        <th>{{$tot_bill}}</th>

                                        <th>{{$tot_received_amount}}</th>

                                        <td></td><td></td>

                                    </tr>

                                </tbody>

                              



                            </table>

                        </div>

                    </div>

                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-lg-12 text-right">

                            <!-- <a href="http://app.washup.com.pk/cal_dis_time" class="btn btn-primary mr-2">Get Route & Save</a> -->

                        </div>

                    </div>

                </div>

                <!--end::Form-->

            </div>

            <!--end: Datatable-->

        </div>

    </div>



    

    <script>

        $(".search_div").hide();

        $(document).ready(function () { 

            // BEGIN::initialization of datatable

            var order_table = $('#order_table').DataTable({

                "aaSorting": [],

                "paging":   false,

                dom: 'Bfrtip',

                buttons: [

                     'csv'

                ],

                // "info":     false,

                "processing": false,

                "columnDefs": [ {

                "targets": [0,1,2,3,4,5,6,7,8,9,10,11],

                "orderable": false

                } ]

            });



            var rides_table = $('#rides_table').DataTable({

                "aaSorting": [],

                "paging":   false,

                dom: 'Bfrtip',

                buttons: [

                     'csv'

                ],

                // "info":     false,

                "processing": false,

                "columnDefs": [ {

                "targets": [0,1,2,3,4,5,6,7,8,9],

                "orderable": false

                } ]

            });

        });
        
$(document).ready(function() {

    function createEditButton() {
        // Check if an edit button already exists

        // Create the edit button
        var $editBtn = $('<button style="margin-left:2%"class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-pencil-alt"></i></button>');

        // Attach click event handler to the edit button
        $editBtn.click(function() {
            var $tr = $(this).closest('tr');
            var $startReadingTd = $tr.find('.start-reading');
            var $ids = $tr.find('.ids');
            var oldValue = $startReadingTd.text().trim(); // Fetching old value from data attribute
            var riderId = $tr.data('record-id'); // Fetching rider's ID from data attribute

            // Detach the edit button from the DOM
            $(this).detach();

            // Create the input field
            var $input = $('<input type="number" class="form-control"  value="' + oldValue + '">');

            // Replace the content of the td with the input field
            $startReadingTd.html($input);

            // Focus on the input field after replacing the content
            $input.focus();

            // Listen for Enter key press event on the input field
            $input.keypress(function(event) {
                if (event.keyCode === 13) { // Check if the Enter key is pressed
                    var newValue = $(this).val(); // Fetching new value from input field

                    // AJAX call to send data to the controller
                    $.ajax({
                        url: '/update_start_reading',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            riderId: riderId,
                            oldValue: oldValue,
                            newValue: newValue,
                            userId: {{$user_id}}, // Pass the user_id from Blade template
                        },
                        success: function(response) {
                            console.log("Success");

                            // Hide the old and new tr elements
                            $tr.next().find('.old-start-reading').hide();
                            $tr.next().find('.old-end-reading').hide();
                            $tr.next().next().find('.new-start-reading').hide();
                            $tr.next().next().find('.new-end-reading').hide();
                            // Create a new tr for the old value
                         // Create a new tr for the old value
                            var $oldTr = $('<tr><td class="old-start-reading" style="text-align: center;">Old Reading: ' + oldValue + '</td>' + 
                                 (response.oldEnd ? '<td class="old-start-reading" style="text-align: center;">Old Reading: ' + response.oldEnd + '</td>' : '<td> </td>') + 
                                 '</tr>');


                            // Create a new tr for the updated value
                            // Create a new tr for the updated value
// Create a new tr for the updated value
var $newTr = $('<tr><td class="new-start-reading" style="text-align: center;">' + 
               '<span class="user" style="display: inline-block; padding:5px;width: 20%; margin-left:2%;height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">'+response.update+'</span>' +
               '<span class="date" style="display: inline-block;padding:5px;width: 20%; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span></td>' + 
               (response.oldEnd ? '<td class="new-start-reading" style="text-align: center;">' +
                                   '<span class="user" style="display: inline-block; padding:5px;width: 20%; margin-left:2%;height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">'+response.update+'</span>' +
                                   '<span class="date" style="display: inline-block;padding:5px;width: 20%; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span></td>' : '') + 
               '</tr>');



                            // Insert the new trs after the current tr
                            $tr.after($newTr);
                            $tr.after($oldTr);

                            // Show the updated values
                            $oldTr.show();
                            $newTr.show();

                            // Restore the original content
                            $startReadingTd.text(newValue);

                            // Create a new edit button
                            createEditButton();
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });

                    // Remove the input field and restore the original content
                    $input.remove();
                    $startReadingTd.text(oldValue);
                }
            });
        });

        // Append the edit button to the td
        $('.start-reading').append($editBtn);
    }

    // Initialize the edit button
    createEditButton();
});



$(document).ready(function() {

    function createEditButton() {
        // Check if an edit button already exists

        // Create the edit button
        var $editBtn = $('<button  style="margin-left:2%"class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-pencil-alt"></i></button>');

        // Attach click event handler to the edit button
        $editBtn.click(function() {
            var $tr = $(this).closest('tr');
            var $endReadingTd = $tr.find('.end-reading');
            var $ids = $tr.find('.ids');
            var oldValue = $endReadingTd.text().trim(); // Fetching old value from data attribute
            var riderId = $tr.data('record-id'); // Fetching rider's ID from data attribute

            // Detach the edit button from the DOM
            $(this).detach();

            // Create the input field
            var $input = $('<input type="number" class="form-control"  value="' + oldValue + '">');

            // Replace the content of the td with the input field
            $endReadingTd.html($input);

            // Focus on the input field after replacing the content
            $input.focus();

            // Listen for Enter key press event on the input field
            $input.keypress(function(event) {
                if (event.keyCode === 13) { // Check if the Enter key is pressed
                    var newValue = $(this).val(); // Fetching new value from input field

                    // AJAX call to send data to the controller
                    $.ajax({
                        url: '/update_end_reading',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            riderId: riderId,
                            oldValue: oldValue,
                            newValue: newValue,
                            userId: {{$user_id}}, // Pass the user_id from Blade template
                        },
                        success: function(response) {
                            console.log("Success");

                            // Hide the old and new tr elements
                            $tr.next().find('.old-start-reading').hide();
                            $tr.next().find('.old-end-reading').hide();
                            $tr.next().next().find('.new-start-reading').hide();
                            $tr.next().next().find('.new-end-reading').hide();
                            // Create a new tr for the old value
                           // Create a new tr for the old value

var $oldTr = $('<tr><td class="old-start-reading" style="text-align: center;">Old Reading: ' +  response.oldStart + '</td>' + 
                                 (response.oldStart ? '<td class="old-start-reading" style="text-align: center;">Old Reading: ' + oldValue + '</td>' : '<td> </td>') + 
                                 '</tr>');
                            // Create a new tr for the updated value
                           // Create a new tr for the updated value
var $newTr = $('<tr><td class="new-start-reading" style="text-align: center;">' + 
               '<span class="user" style="display: inline-block; padding:5px; margin-left:20%;width: 20%; height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">'+response.update+'</span>' +
               '<span class="date" style="display: inline-block;padding:5px;width: 20%; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span></td>' +
               '<td class="new-start-reading" style="text-align: center;">' +
               '<span class="user" style="display: inline-block; padding:5px; margin-left:20%;width: 20%; height: 40%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">'+response.update+'</span>' +
               '<span class="date" style="display: inline-block;padding:5px;width: 20%; height: 50%; background-color: grey; color:white;text-align: center; line-height: 100%; border-radius: 25px;">{{$record->updated_at}}</span></td></tr>');


                            // Insert the new trs after the current tr
                            $tr.after($newTr);
                            $tr.after($oldTr);

                            // Show the updated values
                            $oldTr.show();
                            $newTr.show();

                            // Restore the original content
                            $endReadingTd.text(newValue);

                            // Create a new edit button
                            createEditButton();
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });

                    // Remove the input field and restore the original content
                    $input.remove();
                    $endReadingTd.text(oldValue);
                }
            });
        });

        // Append the edit button to the td
        $('.end-reading').append($editBtn);
    }

    // Initialize the edit button
    createEditButton();
});


    </script>

@endsection

