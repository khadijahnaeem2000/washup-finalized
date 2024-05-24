@extends('layouts.master')
@section('title','Order')
@section('content')

  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Show @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="{{ route('orders.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td width="50%">Order No</td>
                                        <td>{{$data->id}}</td>
                                    </tr>
                                    
                                    @if(isset($data->ref_order_id))
                                        <tr>
                                            <td>Ref Order No</td>
                                            <td>{{$data->ref_order_id}}</td>
                                        </tr>
                                    @endif
                                    
                                    <tr>
                                        <td>Customer Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Contact# </td>
                                        <td>{{$data->contact_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickup Date</td>
                                        <td>{{$data->pickup_date}}</td>
                                    </tr>

                                    <tr>
                                        <td>Delivery Date</td>
                                        <td>{{$data->delivery_date}}</td>
                                    </tr>

                                    <tr>
                                        <td>Permanent Note</td>
                                        <td>{{$data->permanent_note}}</td>
                                    </tr>

                                    <tr>
                                        <td>Order Note</td>
                                        <td>{{$data->order_note}}</td>
                                    </tr>

                                    @if(isset($data->pickup_address))
                                        <tr>
                                            <td>Pickup Address</td>
                                            <td>{{$data->pickup_address}}</td>
                                        </tr>
                                    @endif
                                    
                                    @if(isset($data->delivery_address))
                                        <tr>
                                            <td>Drop off/ Pick & Drop Address</td>
                                            <td>{{$data->delivery_address}}</td>
                                        </tr>
                                    @endif
                                    
                                    @if(isset($data->pickup_timeslot))
                                        <tr>
                                            <td>Pickup Timeslot</td>
                                            <td>{{$data->pickup_timeslot}}</td>
                                        </tr>
                                    @endif
                                    

                                    @if(isset($data->delivery_timeslot))
                                        <tr>
                                            <td>Drop off/ Pick & Drop Timeslot</td>
                                            <td>{{$data->delivery_timeslot}}</td>
                                        </tr>
                                    @endif
                                    

                                    <tr>
                                        <td>Status</td>
                                        <td>
                                                @if($data->status_name)
                                                    {{$data->status_name}}
                                                @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Waive Delivery</td>
                                        <td>
                                                @if($data->waver_delivery === 1)
                                                    Yes
                                                @else
                                                No
                                                @endif
                                        </td>
                                    </tr>
                                      <tr>
                                        <td>Phase/Screen</td>
                                        <td>
                                                @if($data->phase)
                                                    {{$data->phase}}
                                                @endif
                                        </td>
                                    </tr>
                                      <tr>
                                        <td>Waive Delivery Date</td>
                                        <td>
                                                @if($data->DW_when)
                                                    {{$data->DW_when}}
                                                @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Waive Delivery By Whom</td>
                                        <td>
                                                @if($data->DW_who)
                                                    {{$data->order_DW_who}}
                                                @endif
                                        </td>
                                    </tr>
                                    @if(isset($data->washhouse_name))
                                        <tr>
                                            <td>Wash house </td>
                                            <td><b>{{$data->washhouse_name}}</b></td>
                                        </tr>
                                    @endif

                                    @if(isset($polybags))
                                        <tr>
                                            <td>No. of polybags </td>
                                            <td><b>{{$polybags}}</b></td>
                                        </tr>
                                    @endif
                                   

                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($selected_items)
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Order Details</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                    <table class="table dt-responsive">
                                    <tbody>
                                            @if($selected_items)
                                                <?php $id = $selected_items[0]->service_id;?>

                                                    <tr>
                                                        <th colspan=8><h4>Service: {{ $selected_items[0]->service_name }}</h4></th>
                                                    </tr>
                                                    <tr>
                                                        <th width="20%">Name</th>
                                                        <th width="10%">Pickup Qty</th>
                                                        <th width="10%">Scan Qty</th>
                                                        <th width="10%">BT Qty</th>
                                                        <th width="10%">NR Qty</th>
                                                        <th width="10%">HFQ Qty</th>
                                                        <th width="10%">Addon </th>
                                                        <th width="20%">Note </th>
                                                    </tr>
                                                    

                                                    @foreach($selected_items as $value)
                                                        <?php if ($id == $value->service_id){ ?>
                                                            <tr>
                                                                <td>{{ $value->item_name }}</td>
                                                                <td>{{ $value->pickup_qty }}</td>
                                                                <td>{{ $value->scan_qty }}</td>
                                                                <td>{{ $value->bt_qty }}</td>
                                                                <td>{{ $value->nr_qty }}</td>
                                                                <td>{{ $value->hfq_qty }}</td>
                                                                
                                                                <td>
                                                                    @foreach($selected_addons as $add_value)
                                                                        @if(($add_value->service_id == $id) && ($add_value->item_id ==$value->item_id ))
                                                                            {{ $add_value->addon_name }}, 
                                                                        @endif
                                                                    @endforeach
                                                                </td>

                                                                
                                                                <td>{{ $value->note }}</td>
                                                            </tr>
                                                        <?php }else{
                                                            $id = $value->service_id;
                                                            ?>
                                                            <tr>
                                                                <th  colspan=8> <h4> Service: {{ $value->service_name }}</h4></th>
                                                            </tr>
                                                            <tr>
                                                                <th width="20%">Name</th>
                                                                <th width="10%">Pickup Qty</th>
                                                                <th width="10%">Scan Qty</th>
                                                                <th width="10%">BT Qty</th>
                                                                <th width="10%">NR Qty</th>
                                                                <th width="10%">HFQ Qty</th>
                                                                <th width="10%">Addon </th>
                                                                <th width="20%">Note </th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $value->item_name }}</td>
                                                                <td>{{ $value->pickup_qty }}</td>
                                                                <td>{{ $value->scan_qty }}</td>
                                                                <td>{{ $value->bt_qty }}</td>
                                                                <td>{{ $value->nr_qty }}</td>
                                                                <td>{{ $value->hfq_qty }}</td>
                                                                <td>
                                                                    @foreach($selected_addons as $add_value)
                                                                        @if(($add_value->service_id == $id) && ($add_value->item_id ==$value->item_id ))
                                                                            {{ $add_value->addon_name }},
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                
                                                                <td>{{ $value->note }}</td>
                                                            </tr>


                                                        <?php }?>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan=2><label class="badge badge-info">{{ "N/A" }} </label></td>
                                                    </tr>
                                                    
                                                @endif
                                        <tbody>
                                        

                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
                                                    
            @if($details)
                <div class="card card-custom gutter-b example example-compact">
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Summary</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                        <table class="table dt-responsive">
                                        <thead>
                                            <tr>
                                                <th width="50%">Service Name</th>
                                                <th width="10%">Weight</th>
                                                <th width="10%">Pickup Qty</th>
                                                <th width="10%">Delivery Qty</th>
                                                <th width="10%">HFQ Qty</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        @if($details)
                                    
                                        <?php $tot_pickup = 0 ; $tot_weight=0; $tot_delivery = 0; $tot_hfq = 0;  $delivery_qty = 0 ;?>
                                            <tbody>
                                                @foreach($details as $value)
                                                    <?php 
                                                        $delivery_qty    = $value['scan_qty'] + $value['bt_qty'] + $value['nr_qty'];
                                                        $tot_weight     += $value['service_weight'];
                                                        $tot_pickup     += $value['pickup_qty']; 
                                                        $tot_hfq        += $value['hfq_qty']; 
                                                        $tot_delivery   += $delivery_qty ; 
                                                    ?>
                                                    <tr>
                                                        <th>{{ $value['service_name'] }}</th>
                                                        <td> {{ $value['service_weight'] }}</td>
                                                        <td> {{ $value['pickup_qty'] }} </td>
                                                        <td> {{ $delivery_qty }}  </td>
                                                        <td> {{ $value['hfq_qty'] }}</td>
                                                        <td> </td>
                                                    </tr>
                                                @endforeach
                                            <tbody>
                                        @endif
                                        
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th>{{ $tot_weight }}</th>
                                                <th>{{ $tot_pickup}}</th>
                                                <th>{{ $tot_delivery}}</th>
                                                <th>{{ $tot_hfq}}</th>
                                                <td></td>
                                            </tr>
                                        </tfoot>

                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            @if($histories)
                <div class="card card-custom gutter-b example example-compact d-print-none">
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Order History</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                        <table class="table dt-responsive">
                                            <thead>
                                                <tr>
                                                    
                                                    <th width="20%">User</th>
                                                    <th width="20%">Role</th>
                                                    <th width="20%">Status Name</th>
                                                    <th width="20%">Timestamp</th>
                                                    <th width="20%">View Trial</th>
                                                    
                                                </tr>
                                            </thead>
                                            @if($histories)
                                                <tbody>
                                                    @foreach($histories as $value)
                                              
                                                        <tr>
                                                            <th>{{ $value->user_name }} </th>
                                                            <th>{{ $value->role_name }} </th>
                                                            <th>{{ $value->status_name }}
                                                                    <?php
                                                                        $msg = "";

                                                                        if((isset($value->status_id)) && (($value->status_id) == 16)) {
                                                                            
                                                                            if(isset($value->detail)){
                                                                                
                                                                        
                                                                                $dataa = (json_decode(($value->detail), true));
                                                                                if(array_key_exists( 'reason',$dataa )){
                                                                                    
                                                                               
                                                                                $msg    = $dataa['reason'];
                                                                                
                                                                                if($msg != ""){
                                                                                
                                                                         
                                                                       

                                                                        ?>
                                                                                    <span class="btn btn-secondary btn-sm"  data-toggle="tooltip" data-placement="top" title="{{$msg}}">
                                                                                        <i class="fas fa-comment-alt" ></i>
                                                                                    </span>
                                                                    <?php 
                                                                          } }} }
                                                                    ?>
                                                            </th>
                                                            <td>{{ $value->created_at }}</td>
                                                            <td>
                                                                @if((isset($value->detail)) && ($value->detail != null)  )
                                                                    <a href="/orders/fetch_history/{{ $value->history_id }}" class="btn btn-primary btn-sm"> View</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                <tbody>
                                            @endif
                                    
                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
       </div>
   </div>

   
@endsection
