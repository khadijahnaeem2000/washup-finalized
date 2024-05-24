@extends('layouts.master')
@section('title','Order Details')
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
                        <a  href="{{ route('order_details.index') }}" class="btn btn-primary btn-sm ">
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

                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $data->status_name }}</td>
                                    </tr>
                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

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
                                                        <th colspan=5><center> <h4>Service: {{ $selected_items[0]->service_name }} </h4> <span >Weight: {{ $weight }} KG </span></center></th>
                                                    </tr>
                                                    <tr>
                                                        <th width="35%">Item Name</th>
                                                        <th width="10%">Item Qty</th>
                                                        <th width="25%">Addon </th>
                                                        <!-- <th width="20%">Image </th> -->
                                                        <th width="30%">Note </th>
                                                    </tr>
                                                    

                                                    @foreach($selected_items as $value)
                                                        <?php if ($id == $value->service_id){ ?>
                                                            <tr>
                                                                <td>{{ $value->item_name }}</td>
                                                                <td>{{ $value->pickup_qty }}</td>
                                                                
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
                                                        <?php }else{
                                                            $id = $value->service_id;
                                                            foreach($selected_services as $skey =>$svalue) {
                                                                if($id == $svalue->service_id){
                                                                    $weight = $svalue->weight;
                                                                }
                                                            }
                                                            ?>
                                                            <tr>
                                                                <th  colspan=5><center> <h4> Service: {{ $value->service_name }}</h4> <span>Weight: {{ $weight }} KG </span></center></th>
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
                                                                <td>{{ $value->pickup_qty }}</td>
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

            @if(isset($histories))
                <div class="card card-custom card-collapsed d-print-none" data-card="true">
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Order History</h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
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
                                            @if(isset($histories))
                                                <tbody>
                                                    @foreach($histories as $value)
                                                    
                                                        <tr>
                                                            <th>{{ $value->user_name }} </th>
                                                            <th>{{ $value->role_name }} </th>
                                                            <th>{{ $value->status_name }} </th>
                                                            <td>{{ $value->created_at }}</td>
                                                            <td>
                                                                @if((isset($value->detail)) && ($value->detail != null)  ) 
                                                                    <a href="/order_details/fetch_history/{{ $value->history_id }}" class="btn btn-primary btn-sm"> View</a>
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
