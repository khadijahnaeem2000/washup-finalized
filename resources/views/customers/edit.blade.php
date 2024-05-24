@extends('layouts.master')
@section('title','Customers')
@section('content')
@include( '../sweet_script')

    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=places"></script>  
    <style type="text/css">
        #map_canvas {
            width: 100%;
            height: 500px;
        }
        #current {
            padding-top: 25px;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('customers.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['customers.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                    {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','autofocus' => ''  )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('email','Email ')) !!}
                                    {{ Form::email('email', null, array('placeholder' => 'Enter valid email','class' => 'form-control')) }}
                                    @if ($errors->has('email'))  
                                        {!! "<span class='span_danger'>". $errors->first('email')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">

                                    {!! Html::decode(Form::label('in_amount','Current Amount ')) !!}
                                    @if($selected_wallets)
                                        {{ Form::number('in_amount', $selected_wallets->net_amount, array('placeholder' => 'Enter current amount','class' => 'form-control','readonly'=>'true')) }}
                                    @else
                                        {{ Form::number('in_amount', 0, array('placeholder' => 'Enter current amount','class' => 'form-control')) }}
                                    @endif
                                    
                                    @if ($errors->has('in_amount'))  
                                        {!! "<span class='span_danger'>". $errors->first('in_amount')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Contact# <span class="text-danger">*</span>')) !!}
                                    {{ Form::text('contact_no', null, array('placeholder' => '03XXXXXXXXX','class' => 'form-control','required'=>'true','pattern'=>'[0][3][0-9][0-9][0-9]{7}', 'title'=>'Please enter valid phone number: 03XXXXXXXXX')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('alt_contact_no','Alt Contact# ')) !!}
                                    {{ Form::text('alt_contact_no', null, array('placeholder' => '03XXXXXXXXX','class' => 'form-control','pattern'=>'[0][3][0-9][0-9][0-9]{7}', 'title'=>'Please enter valid phone number: 03XXXXXXXXX')) }}
                                    @if ($errors->has('alt_contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('alt_contact_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('customer_type_id','Customer Type <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('customer_type_id', $customer_types,null, array('class' => 'form-control','onchange'=>'show_items(this.value)' )) !!}
                                    @if ($errors->has('customer_type_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('customer_type_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('permanent_note','Permanent Note ')) !!}
                                    {{ Form::text('permanent_note', null, array('placeholder' => 'Enter permanent note','class' => 'form-control','autofocus' => ''  )) }}
                                    @if ($errors->has('permanent_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('permanent_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h4 class="card-title">Addresses <span class="text-danger">*</span></h4>
                        @if ($errors->has('address'))  
                            {!! "<span class='span_danger'>". $errors->first('address')."</span>"!!} 
                        @endif
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="addressTable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="60%">Address </th>
                                                <th width="10%">Latitude </th>
                                                <th width="10%">Longitude </th>
                                                <th width="15%">Status</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($selected_addresses)){
                                                    foreach($selected_addresses as $key => $value){ ?>
                                                    <script type="text/javascript">
                                                        $rowno=$("#addressTable tr").length;
                                                        if($rowno==1){
                                                            $rowno=$rowno+1;
                                                            $("#addressTable tr:last").after("<tr id='row_addressTable"+$rowno+"'>"+
                                                                    "<td> " +
                                                                        '<input id="address[]"   name="address[]"    value="{{$value->address}}"   placeholder="Address"  class ="form-control" required/>'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '<input id="latitude[]"  name="latitude[]"   value="{{$value->latitude}}"  placeholder="Latitude" class ="form-control" readonly/>'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '<input id="longitude[]" name="longitude[]"  value="{{$value->longitude}}" placeholder="Longitude" class ="form-control" readonly/>'+
                                                                    "</td>"+
                                                                    
                                                                    "<td>  "+
                                                                        '{!! Form::select("status[]", ["0"=>"Primary"],$value->status, array("class"=> "form-control")) !!}'+
                                                                    "</td>"+

                                                                    "<td  width='40px'>"+
                                                                        "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_address_row('row_addressTable"+$rowno+"')>"+
                                                                    "</td>"+
                                                            "</tr>");
                                                        }else{
                                                            $rowno=$rowno+1;
                                                            $("#addressTable tr:last").after("<tr id='row_addressTable"+$rowno+"'>"+
                                                                    "<td> " +
                                                                        '<input id="address[]"   name="address[]"    value="{{$value->address}}"   placeholder="Address"  class ="form-control" required/>'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '<input id="latitude[]"  name="latitude[]"   value="{{$value->latitude}}"  placeholder="Latitude" class ="form-control" readonly/>'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '<input id="longitude[]" name="longitude[]"  value="{{$value->longitude}}" placeholder="Longitude" class ="form-control" readonly/>'+
                                                                    "</td>"+
                                                                    
                                                                    "<td>  "+
                                                                        '{!! Form::select("status[]", ["1"=>"Secondary"],$value->status, array("class"=> "form-control")) !!}'+
                                                                    "</td>"+

                                                                    "<td  width='40px'>"+
                                                                        "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_address_row('row_addressTable"+$rowno+"')>"+
                                                                    "</td>"+
                                                            "</tr>");
                                                        }
                                                    </script>
                                            <?php } }?>
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        {!! Html::decode(Form::label('address_name','Search Address ')) !!}
                        
                        <div class="form-group row">
                            <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                {{ Form::text('address_name', null, array('placeholder' => 'Enter area name','class' => 'form-control','autofocus' => '','id'=>'address_name' )) }}
                                @if ($errors->has('address_name'))  
                                    {!! "<span class='span_danger'>". $errors->first('address_name')."</span>"!!} 
                                @endif

                                {{ Form::hidden('radius', 0, array('placeholder' => 'Enter radius in meters','class' => 'form-control','id'=>'radius','onchange'=>'radius_change()')) }}
                                {!! Form::hidden('lat',null, array('placeholder' => 'Enter latitude','class' => 'form-control','id'=>'lat','readonly'=>'true')) !!}
                                {!! Form::hidden('lng',null, array('placeholder' => 'Enter longitude','class' => 'form-control','id'=>'lng','readonly'=>'true')) !!}
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                <input class="btn btn-success btn-sm" type="button" onclick="add_address_row();" value="+">
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div id='map_canvas'></div>
                                <div id="current"></div>
                            </div>
                        </div>


                        <h4 class="card-title">Services</h4>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive" style="max-height:400px">
                                    <table id="serviceTable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="40%">Service Name</th>
                                                <th width="20%">Rate</th>
                                                <th width="10%">Active</th>
                                                <th width="30%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($services as $key => $value)
                                            <?php 
                                                $check = 0;
                                                foreach($selected_services as $service_key => $service_value){
                                                    if(($value->id == $service_value->service_id) && ($service_value->status ==1) ){ 
                                                        $check = 1;
                                                        break;
                                                        }
                                                    }
                                                if($check == 1){
                                            ?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        {!! Form::number("service_rate[$value->id]", $service_value->service_rate, array("placeholder" => "Enter rate","class" => "form-control readonly_class")) !!}
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("service_status[$value->id]", 1, true,array("class" =>  "form-control cls_services",'data-pnt'=>$value->id)) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>
                                                <?php }else{?>
                                                    <tr>
                                                        <td>
                                                            {{ $value->name }}
                                                        </td>
                                                        <td>
                                                            {!! Form::number("service_rate[$value->id]", $value->rate, array("placeholder" => "Enter rate","class" => "form-control readonly_class")) !!}
                                                        </td>
                                                        <td>
                                                            <span class="switch switch-outline switch-icon switch-primary">
                                                                <label>
                                                                    {!! Form::checkbox("service_status[$value->id]", 1, false,array("class" =>  "form-control cls_services",'data-pnt'=>$value->id)) !!}
                                                                    <span></span> 
                                                                </label>
                                                            </span>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            @endforeach
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @foreach($all_special_services as $service_key => $service_value)
                            <div class="cls_<?php echo $service_value->id?>">
                                <?php $service_id = $service_value->id;?>
                                <h4 class="card-title">Special Services ({{$service_value->name}}) </h4>
                                <div style="height:100%;max-height:400px; overflow-y:scroll; overflow-x:hidden;">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table id="special_serviceTable" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th width="40%">Item Name</th>
                                                            <th width="20%">Rate</th>
                                                            <th width="10%">Active</th>
                                                            <th width="30%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php  
                                                        foreach($special_services as $key => $value)
                                                        {
                                                            if($value->id == $service_id )
                                                            { ?>
                                                                <?php 
                                                                $check = 0;
                                                                foreach($selected_special_services as $item_key => $item_value){
                                                                    if(($value->item_id == $item_value->item_id) && ($item_value->status ==1) ){ 
                                                                        $check = 1;
                                                                        break;
                                                                        }
                                                                    }
                                                                if($check == 1)
                                                                { ?>
                                                                    <tr>
                                                                        <td>
                                                                            {{ $value->item_name }}
                                                                        </td>
                                                                        <td>
                                                                        <?php
                                                                            $rate = $item_value->item_rate;
                                                                            $status = false;
                                                                            //echo '<pre>';print_r($customer_items);echo '</pre>';
                                                                            if(array_key_exists($service_id, $customer_items)){
                                                                                if(array_key_exists($value->item_id, $customer_items[$service_id])){
                                                                                    $rate = $customer_items[$service_id][$value->item_id]['item_rate'];
                                                                                    if($customer_items[$service_id][$value->item_id]['status'] == 1){
                                                                                        $status = true;
                                                                                    }
                                                                                }
                                                                            
                                                                                //$status = $customer_items[$service_id]['status'];
                                                                            }
                                                                        ?>                                         
                                                                            {!! Form::number("item_rate[$service_id][$value->item_id]", $rate, array("placeholder" => "Enter rate","class" => "form-control readonly_class")) !!}
                                                                        </td>
                                                                        <td>
                                                                            <span class="switch switch-outline switch-icon switch-primary">
                                                                                <label>

                                                                                    {!! Form::checkbox("item_status[$service_id][$value->item_id]", 1, $status,array("class" => "form-control")) !!}
                                                                                    <span></span> 
                                                                                </label>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::hidden("item_service_id[$service_id][$value->item_id]", $value->id, array("class" => "form-control")) !!}
                                                                        </td>
                                                                    </tr>
                                                                    <?php 
                                                                }else{ ?>
                                                                    <tr>
                                                                        <td>
                                                                            {{ $value->item_name }}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::number("item_rate[$service_id][$value->item_id]", $value->item_rate, array("placeholder" => "Enter rate","class" => "form-control readonly_class")) !!}
                                                                        </td>
                                                                        <td>
                                                                            <span class="switch switch-outline switch-icon switch-primary">
                                                                                <label>
                                                                                    {!! Form::checkbox("item_status[$service_id][$value->item_id]", 1, false,array("class" => "form-control")) !!}
                                                                                    <span></span> 
                                                                                </label>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::hidden("item_service_id[$service_id][$value->item_id]", $value->id, array("class" => "form-control")) !!}
                                                                        </td>
                                                                    </tr> 
                                                                    <?php 
                                                                } 
                                                            }else{
                                                                
                                                                //$service_id=  $value->id;
                                                                ?>
                                                                

                                                                <?php 

                                                            }
                                                        } ?>
                                                    
                                                    <tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        @endforeach
                        <hr>
                        <h4 class="card-title">Retainer Days</h4>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive"   style="max-height:400px">
                                    <table id="ratainer_day_table" class="table">
                                        <thead>
                                            <tr>
                                                <th>Days </th>
                                                <th>Timeslots </th>
                                                <th>Note </th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($selected_days)){
                                                    foreach($selected_days as $key => $value){ ?>
                                                    <script type="text/javascript">
                                                        $rowno=$("#ratainer_day_table tr").length;
                                                        $rowno=$rowno+1;
                                                        $("#ratainer_day_table tr:last").after("<tr id='ratainer_day_table_row"+$rowno+"'>"+
                                                                    "<td> " +
                                                                    '{!! Form::select("day[]", $days,$value->day_id, array("class"=> "form-control")) !!}'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '{!! Form::select("time_slot[]", $time_slots,$value->time_slot_id, array("class"=> "form-control")) !!}'+
                                                                    "</td>"+
                                                                    "<td> " +
                                                                        '{!! Form::text("note[]", $value->note, array("placeholder" => "Enter note","class" => "form-control")) !!}'+
                                                                    "</td>"+

                                                                    "<td  width='40px'>"+
                                                                        "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_day_row('ratainer_day_table_row"+$rowno+"')>"+
                                                                    "</td>"+
                                                            "</tr>");
                                                    </script>
                                            <?php } }?>
                                        <tbody>
                                    </table>
                                </div>

                                <table id="" class="table">
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="text-align:right"> Add New Row</td>
                                            <td width="5%"><input class="btn btn-success btn-sm" type="button" onclick="add_day_row();" value="+"></td>
                                        </tr>
                                    <tbody>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        <h4 class="card-title">Message Alerts</h4>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive" style="max-height:400px">
                                    <table id="messageTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Message</th>
                                                <th>Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($messages as $key => $value)
                                            <?php 
                                                $check = 0;
                                                foreach($selected_messages as $message_key => $message_value){
                                                    if($value->id == $message_value->message_id){ 
                                                        $check = 1;
                                                        break;
                                                        }
                                                    }
                                                if($check == 1){
                                            ?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("message[]", $value->id, true,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <?php }else{?>
                                                    <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("message[]", $value->id, false,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>
                                                    
                                                <?php }?>
                                            @endforeach
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h4 class="card-title">Email Alerts</h4>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive" style="max-height:400px">
                                    <table id="messageTable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="60%">Email on Order verification</th>
                                                <th width="40%">
                                                    <span class="switch switch-outline switch-icon switch-primary">
                                                        <label>
                                                            {!! Form::checkbox("email_alert",1, null,array("class" => "form-control")) !!}
                                                            <span></span> 
                                                        </label>
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>
                                      
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <!-- <button type="reset" class="btn btn-secondary">Reset</button> -->
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>


    <!-- show/hide the services -->
    <script>
        $(".cls_services").click(function(){
            var id          = $(this).attr('data-pnt');
            var cls_name    = ".cls_"+id;
            if ($(this).prop("checked")) {
                // console.log(cls_name);
                $(cls_name).show(200);
            } else { 
                // console.log(cls_name); 
                $(cls_name).hide(200);
            } 
        });
    </script>


    <!-- Google Map Location -->
    <script type="text/javascript">
        // LOCATION IN LATITUDE AND LONGITUDE.
        $( '#address_name' ).on( 'keypress', function( e ) {
            if( e.keyCode === 13 ) {
                e.preventDefault();
            }
        });
        var lat     = 24.84284731180348 ;
        var lng     = 67.06590673068848;
        var radius = 0;
        document.getElementById('lat').value =lat;
        document.getElementById('lng').value =lng;
        document.getElementById('radius').value =radius;
        
        document.getElementById('map_canvas')
        var center = new google.maps.LatLng(lat, lng);
        

        const geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 14,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var marker = new google.maps.Marker({
            position: center,
            draggable: true
        });
        
        var circle = new google.maps.Circle({
                center: center,
                map: map,
                radius: radius,          // IN METERS.
                strokeOpacity: 0.8,
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 0         // DON'T SHOW CIRCLE BORDER.
            });

     
        
        function initialize(lat, lng,radius) {
            
            circle = new google.maps.Circle({
                center: new google.maps.LatLng(lat, lng),
                map: map,
                radius: radius,          // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 0         // DON'T SHOW CIRCLE BORDER.
            });
        }


        var input = document.getElementById('address_name');  
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);  
        var searchBox = new google.maps.places.SearchBox(input);

        google.maps.event.addListener(searchBox, 'places_changed', function() {  
            var places = searchBox.getPlaces();  
            place = places[0];
            var posit = place.geometry.location;
            var lat = posit.lat();
            var lng = posit.lng();

            document.getElementById('lat').value =lat;
            document.getElementById('lng').value =lng;
            marker.setMap(null);

            marker.position= new google.maps.LatLng(lat, lng);
            map.setCenter(marker.position);
            marker.setMap(map);
            


            /// Start Extract Address from the LAT & LNG ///
            const latlng = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng),
                };

            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === "OK") {
                if (results[0]) {
                    // console.log(results[0].formatted_address);
                    document.getElementById('address_name').value =results[0].formatted_address;
                } else {
                    window.alert("No results found");
                }
                } else {
                window.alert("Geocoder failed due to: " + status);
                }
            });
            /// End Extract Address from the LAT & LNG ///

        });

        

        google.maps.event.addListener(marker, 'dragend', function (evt) {
            document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat() + ' Current Lng: ' + evt.latLng.lng() + '</p>';
            // var lat =evt.latLng.lat();
            // var lng =evt.latLng.lng();
            var lat =evt.latLng.lat();
            var lng =evt.latLng.lng();
            document.getElementById('lat').value =lat;
            document.getElementById('lng').value =lng;
            radius = parseInt(document.getElementById('radius').value);
            circle.setMap(null);
            initialize(lat, lng,radius);

            /// Start Extract Address from the LAT & LNG ///
            const latlng = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng),
                };

            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === "OK") {
                if (results[0]) {
                    // console.log(results[0].formatted_address);
                    document.getElementById('address_name').value =results[0].formatted_address;
                } else {
                    window.alert("No results found");
                }
                } else {
                window.alert("Geocoder failed due to: " + status);
                }
            });
            /// End Extract Address from the LAT & LNG ///

        });

        google.maps.event.addListener(marker, 'dragstart', function (evt) {
            document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
        });
    
        map.setCenter(marker.position);
        marker.setMap(map);

    </script>
    
    <!-- Customer Type Event -->
    <script type="text/javascript">
       $(document).ready(function(){
            // show / hide the special services block 
            $(".cls_services").each(function(){
                var id          = $(this).attr('data-pnt');
                var cls_name    = ".cls_"+id;
                if ($(this).prop("checked")) {
                    // console.log(cls_name);
                    $(cls_name).show(200);
                } else { 
                    // console.log(cls_name); 
                    $(cls_name).hide(200);
                } 
            });

            var customer_type_id = document.getElementById('customer_type_id').value;  
            show_items(customer_type_id);
         
            
        })
    </script>

    <!-- address table -->
    <script type="text/javascript">
        function show_items($id){
            // alert($id);
            if($id==1){
           
                $('.readonly_class').attr("readonly", "true");
            }else{
                $('.readonly_class').removeAttr("readonly");
            }
        //    alert("adf");
        }
        function add_address_row(){
            $rowno=$("#addressTable tr").length;
            var check = 0;
            var address     = document.getElementById('address_name').value;
            if(address) {
               check = 1;
            }else{
                alert("Please select a location");
                $( "#address_name" ).focus();
            }
            if(check ==1){
                var latitude    = document.getElementById('lat').value;  
                var longitude   = document.getElementById('lng').value;  
                if((lat==latitude) && (lng==longitude)){
                    alert("Please select a location");
                    $( "#address_name" ).focus();

                }else{    
                    if($rowno==1){
                        $rowno=$rowno+1;
                        $("#addressTable tr:last").after("<tr id='row_addressTable"+$rowno+"'>"+
                                "<td> " +
                                    '<input id="address[]"   name="address[]"    value=""   placeholder="Address"  class ="form-control" required />'+
                                "</td>"+
                                "<td> " +
                                    '<input id="latitude[]"  name="latitude[]"   value="'+latitude+'"  placeholder="Latitude" class ="form-control" readonly/>'+
                                "</td>"+
                                "<td> " +
                                    '<input id="longitude[]" name="longitude[]"  value="'+longitude+'" placeholder="Longitude" class ="form-control" readonly/>'+
                                "</td>"+
                                "<td>  "+
                                    '{!! Form::select("status[]", ["0"=>"Primary"],null, array("class"=> "form-control", "readonly"=>"true")) !!}'+
                                "</td>"+

                                "<td  width='40px'>"+
                                    "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_address_row('row_addressTable"+$rowno+"')>"+
                                "</td>"+
                        "</tr>");
                    }else{
                        $rowno=$rowno+1;
                        $("#addressTable tr:last").after("<tr id='row_addressTable"+$rowno+"'>"+
                        "<td> " +
                                    '<input id="address[]"   name="address[]"    value=""   placeholder="Address"  class ="form-control" required />'+
                                "</td>"+
                                "<td> " +
                                    '<input id="latitude[]"  name="latitude[]"   value="'+latitude+'"  placeholder="Latitude" class ="form-control" readonly/>'+
                                "</td>"+
                                "<td> " +
                                    '<input id="longitude[]" name="longitude[]"  value="'+longitude+'" placeholder="Longitude" class ="form-control" readonly/>'+
                                "</td>"+
                            
                                "<td> " +
                                    '{!! Form::select("status[]", ["1"=>"Secondary"],null, array("class"=> "form-control")) !!}'+
                                "</td>"+

                                "<td  width='40px'>"+
                                    "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_address_row('row_addressTable"+$rowno+"')>"+
                                "</td>"+
                        "</tr>");
                    }
                }
            }
            
            
        }
        function delete_address_row(rowno){
            $('#'+rowno).remove();
        }

        function add_day_row(){
            $rowno=$("#ratainer_day_table tr").length;
            // alert($rowno);
            $rowno=$rowno+1;
            $("#ratainer_day_table tr:last").after("<tr id='ratainer_day_table_row"+$rowno+"'>"+
                        "<td> " +
                        '{!! Form::select("day[]", $days,null, array("class"=> "form-control")) !!}'+
                        "</td>"+
                        "<td> " +
                            '{!! Form::select("time_slot[]", $time_slots,null, array("class"=> "form-control")) !!}'+
                        "</td>"+
                        "<td> " +
                            '{!! Form::text("note[]", null, array("placeholder" => "Enter note","class" => "form-control")) !!}'+
                        "</td>"+

                        "<td  width='40px'>"+
                            "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_day_row('ratainer_day_table_row"+$rowno+"')>"+
                        "</td>"+
                "</tr>");
        }
        function delete_day_row(rowno){
            $('#'+rowno).remove();
        }
    </script>



@endsection

