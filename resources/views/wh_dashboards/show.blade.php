@extends('layouts.master')
@section('title','Order Details')
@section('content')

 
    <div class="row" id="main">
        <div class="col-lg-12">
            <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">@yield('title')</h3>
                    </div>
                    <div class="card-toolbar d-print-none" >
                        <div class="btn-group btn-group">
                            <a  href="{{ route('wh_dashboards.index') }}" class="btn btn-primary btn-sm ">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
              

                <div class="card-body">
                
                    <div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Full Name:</label>
                                <h4>{{$data->name}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Order No  :</label>
                                <h4>{{$data->id}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Permanent Note :</label>
                                <h4>{{$data->permanent_note}}</h4>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Order Note  :</label>
                                <h4>{{$data->order_note}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Pickup Date:</label>
                                <h4> {{$data->pickup_date}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Delivery Date:</label>
                                <h4> {{$data->delivery_date}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <?php $tot = 0; $mega_tot = 0;?>
            @if( (isset($selected_items)) && (isset($selected_services)))
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Item Details</h3>
                    </div>
                    </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                <tbody>
                                            @if($selected_items)
                                            <?php
                                                $id = $selected_items[0]->service_id;
                                                foreach($selected_services as $skey =>$svalue) {
                                                    if($id == $svalue->service_id){
                                                        $weight = $svalue->weight;
                                                    }
                                                }
                                            ?>

                                                <tr>
                                                    <th colspan=4><center> <h4>Service: {{ $selected_items[0]->service_name }} </h4> <span >Weight: {{ $weight }} KG </span></center></th>
                                                </tr>
                                                <tr>
                                                    <th width="35%">Item Name</th>
                                                    <th width="10%">Item Qty</th>
                                                    <th width="25%">Addon </th>
                                                    <!-- <th width="20%">Image </th> -->
                                                    <th width="30%">Note </th>
                                                </tr>
                                                

                                                @foreach($selected_items as $value)
                                                    <?php if ($id == $value->service_id){ $tot += ($value->pickup_qty); $mega_tot += ($value->pickup_qty);?>
                                                        <tr>
                                                            <td>{{ $value->item_name }}</td>
                                                            <td>{{ $value->pickup_qty }}</td>
                                                            
                                                            <td>
                                                                @foreach($selected_addons as $add_value)
                                                                    @if(($value->ord_itm_id == $add_value->ord_itm_id))
                                                                        <label class="badge badge-success">{{ $add_value->addon_name }}</label>
                                                                    @endif
                                                                @endforeach
                                                            </td>

                                                            <!-- <td>
                                                                @if($value->item_image)
                                                                    <img src="{{ asset('/uploads/orders/'.$value->item_image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);" height="64" width="64">
                                                                @else
                                                                {{ 'No Image' }}
                                                                @endif
                                                            </td> -->
                                                            <td>{{ $value->note }}</td>
                                                        </tr>
                                                    <?php }else{
                                                        
                                                        $id = $value->service_id;
                                                        foreach($selected_services as $skey =>$svalue) {
                                                            if($id == $svalue->service_id){
                                                                $weight = $svalue->weight;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="background: #e6e6e6;">
                                                            <th>Total</th>
                                                            <th> <?php echo $tot; $tot= 0;?></th>
                                                            <td colspan="2"></td>

                                                        </tr>
                                                        <tr>
                                                            <th  colspan=4><center> <h4> Service: {{ $value->service_name }}</h4> <span>Weight: {{ $weight }} KG </span></center></th>
                                                        </tr>
                                                        <tr>
                                                            <th width="35%">Item Name</th>
                                                            <th width="10%">Item Qty</th>
                                                            <th width="25%">Addon </th>
                                                            <!-- <th width="20%">Image </th> -->
                                                            <th width="30%">Note </th>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ $value->item_name }}</td>
                                                            <td>{{ $value->pickup_qty }} <?php $tot += ($value->pickup_qty); $mega_tot += ($value->pickup_qty); ?></td>
                                                            <td>
                                                                @foreach($selected_addons as $add_value)
                                                                    @if(($value->ord_itm_id == $add_value->ord_itm_id) && ($value->item_id == $add_value->item_id))
                                                                        <label class="badge badge-success">{{ $add_value->addon_name }}</label>  
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                            <!-- <td>
                                                                @if($value->item_image)
                                                                    <img src="{{ asset('/uploads/orders/'.$value->item_image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);" height="64" width="64">
                                                                @else
                                                                    {{ 'No Image' }}
                                                                @endif
                                                            </td> -->
                                                            <td>{{ $value->note }}</td>
                                                        </tr>


                                                    <?php }?>
                                                @endforeach
                                                <tr style="background: #e6e6e6;">
                                                    <th >Total</th>
                                                    <th> <?php echo $tot; ?></th>
                                                    <td colspan="2"></td>

                                                </tr>
                                                    
                                                <tr style="background: #bfbfbf;">
                                                    <th>Grand Total</th>
                                                    <th> <?php echo $mega_tot; ?></th>
                                                    <td colspan="2"></td>
                                                </tr>
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
       </div>
   </div>

 
@endsection
