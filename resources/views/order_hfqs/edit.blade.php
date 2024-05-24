@extends('layouts.master')
@section('title','Order')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">HFQ Order# {{$data->id}} <br>  Ref Order#{{$ref_order_id}}</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('order_hfqs.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['order_hfqs.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">

                        <!-- name, contact no, order and status id -->
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name ')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: ')) !!}
                                    {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'readonly' => 'true')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                           
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('order_id','Order No ')) !!}
                                {{ Form::text('order_id', null, array('placeholder' => 'Enter order no','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('order_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_id')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('status_id','Status')) !!}
                                    {!! Form::select('status_id', $statuses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('status_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('status_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- rating, polybags and delivery date -->
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('iron_rating','Iron Rating <span class="text-danger">*</span> ')) !!}
                                    {!! Form::select('iron_rating', $ratings,null, array('class' => 'form-control','autofocus'=>'true')) !!}
                                    @if ($errors->has('iron_rating'))  
                                        {!! "<span class='span_danger'>". $errors->first('iron_rating')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('softner_rating','Softner Rating <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('softner_rating', $ratings,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('softner_rating'))  
                                        {!! "<span class='span_danger'>". $errors->first('softner_rating')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('polybags_qty','Polybags Qty<span class="text-danger">*</span>')) !!}
                                {{ Form::number('polybags_qty', 1, array('placeholder' => 'Enter Polybags Qty','class' => 'form-control','min'=>'1')) }}
                                    @if ($errors->has('polybags_qty'))  
                                        {!! "<span class='span_danger'>". $errors->first('polybags_qty')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                    {{ Form::date('delivery_date', null, array('placeholder' => 'Enter delivery date','class' => 'form-control','readonly' => 'true')) }}
                                    @if ($errors->has('delivery_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('delivery_date')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Permanent and order Note -->
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('permanent_note','Permanent Note: ')) !!}
                                    {!! Form::textarea('permanent_note', null, array('placeholder' => 'Permanent Note','rows'=>5, 'class' => 'form-control','readonly' => 'true' )) !!}
                                    @if ($errors->has('permanent_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('permanent_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('order_note','Order Note: ')) !!}
                                    {!! Form::textarea('order_note', null, array('placeholder' => 'Order Note','rows'=>5, 'class' => 'form-control','readonly' => 'true')) !!}
                                    @if ($errors->has('order_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>


                        <!-- Service items-->
                        <div id="service_items">
                            <?php foreach($selected_services as $service_key => $service_value){?>
                                <table class="table" id="table<?php echo $service_value->service_id?>">
                                    <thead>
                                        <tr>
                                            <th colspan="7"><center><?php echo $service_value->service_name?> </center></th>							
                                        </tr>
                                        <tr>
                                            <th  style="width: 30%;">Item</th>
                                            <th  style="width: 10%;">Qty</th>
                                            <th  style="width: 10%;">Delivered Qty</th>
                                            <th style="width: 10%;">HFQ Qty</th>	
                                            <th style="width: 20%;">Reason</th>		
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($selected_items as $item_key => $item_value){
                                            if( $service_value->service_id ==  $item_value->service_id){?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" value="<?php echo $service_value->service_id?>" class="form-control" name="service_id[<?php echo $service_value->service_id?>]"/>
                                                    <input type="hidden" class="form-control" readonly  value = "{{ $item_value->item_id }}" name="item_id[<?php echo $service_value->service_id?>][]" >
                                                    <input type="text" class="form-control" readonly  value = "{{ $item_value->item_name }}" name="item_name[<?php echo $service_value->service_id?>][]" >
                                                    
                                                </td>
                                

                                                <td>
                                                    <input type="number" class="form-control"  readonly value="<?php echo $item_value->pickup_qty?>" name="pickup_qty[<?php echo $service_value->service_id?>][]" id ="pickup_qty[<?php echo $service_value->service_id?>][]" onchange="calc_value(<?php echo $service_value->service_id?>)"/>
                                                </td>
                                                <?php 
                                                    $delivered_qty = (($item_value->scan_qty) + ($item_value->nr_qty) + ($item_value->bt_qty));
                                                ?>
                                                <td>
                                                    <input type="number" class="form-control" readonly min="0"  value="<?php echo $delivered_qty  ;?>" name="scan_qty[<?php echo $service_value->service_id?>][]" id ="scan_qty[<?php echo $service_value->service_id?>][]" onchange="calc_value(<?php echo $service_value->service_id?>)"/>
                                                </td>

                                                <td >
                                                    <input type="number" class="form-control calc_num" readonly min="0" value = "<?php if (isset($item_value->hfq_qty)) echo $item_value->hfq_qty; else echo 0;?>" name="hfq_qty[<?php echo $service_value->service_id?>][]" id ="hfq_qty[<?php echo $service_value->service_id?>][]">
                                                </td>
                                                <td >
                                                    <input type="text" class="form-control" readonly value = "<?php echo $item_value->reason; ?>" name="reason[<?php echo $service_value->service_id?>][]" id ="reason[<?php echo $service_value->service_id?>][]" placeholder="Enter reason">
                                                </td>
                                                <!-- <td>
                                                    {!! Form::select('reason_id['.$service_value->service_id.'][]',['Null'=>' --- None ---']+$reasons,$item_value->reason_id, array('class' => 'form-control', 'id'=>'reason_id['.$service_value->service_id.'][]')) !!}
                                                </td> -->
                                                
                                                
                                            </tr>
                                        <?php } }?>
                                    </tbody>
                                </table>
                                <!-- <table id="table_summary<?php echo $service_value->service_id?>" class="table">
                                    <tbody>
                                        <tr>
                                            <th width="25">
                                                Total Quantity
                                            </th>
                                            <td width="25%">
                                                <input type="number" class="form-control" value="<?php echo $service_value->service_qty?>" name="qty[<?php echo $service_value->service_id?>]" id="qty[<?php echo $service_value->service_id?>]" readonly="true" />
                                            </td>
                                        
                                            <th width="25">
                                                Total Weight
                                            </th>
                                            <td width="25%">
                                                <input type="number" step = "any" class="form-control" value="<?php echo $service_value->service_weight?>" name="weight[<?php echo $service_value->service_id?>]" />
                                            </td>
                                        </tr>
                                    <tbody>
                                </table> -->

                            <?php } ?>
                           
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

    
 
   
   
@endsection

