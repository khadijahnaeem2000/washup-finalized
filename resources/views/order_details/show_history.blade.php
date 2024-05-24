@extends('layouts.master')
@section('title','Order History Trial')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">@yield('title')</h3>
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
                                        <td>{{$data->order_id}}</td>
                                    </tr>
                                  
                                    <tr>
                                        <td>User</td>
                                        <td>{{ $data->user_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Role</td>
                                        <td>{{ $data->role_name }}</td>
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
            @if((isset($detail)) && ($detail!=null) )
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
                                            @if(isset($detail->services))
                                                @foreach($detail->services as $key => $service_value)
                                                    @if((isset($service_value)) && (isset( $service_value->service_weight )))
                                                        <tr>
                                                            <th colspan=5><center> <h4>Service: {{ $service_value->service_name }} </h4> <span >Weight: @if( isset( $service_value->service_weight )) {{ $service_value->service_weight }} KG @endif </span></center></th>
                                                        </tr>
                                                        <tr>
                                                            <th width="25%">Item Name</th>
                                                            <th width="25%">Item Qty</th>
                                                            <th width="25%">Addon </th>
                                                            <th width="25%">Note </th>
                                                        </tr>

                                                        @if(isset($service_value->items))
                                                            @foreach($service_value->items as $item_key => $item_value)
                                                                @if($service_value->service_id == $item_value->service_id)
                                                                    <tr>
                                                                        <td>{{ $item_value->item_name }}</td>
                                                                        <td>{{ $item_value->pickup_qty }}</td>
                                                                        <td>
                                                                            @foreach($service_value->addons as $addon_key => $addon_value)
                                                                                @foreach($addon_value as $adn_key => $adn_value)
                                                                                    @if($item_value->ord_itm_id == $adn_value->ord_itm_id)
                                                                                        <label class="badge badge-success">{{ $adn_value->addon_name }}</label>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endforeach
                                                                        </td>
                                                                        <td>{{ $item_value->note }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

           
       </div>
   </div>

   
@endsection
