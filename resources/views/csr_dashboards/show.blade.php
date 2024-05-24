@extends('layouts.master')
@section('title','Order')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">
                            Show
                            @if($data->ref_order_id)
                                HFQ
                            @endif
                            @yield('title')
                        </h3>
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
                                    @if($data->ref_order_id)
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

                                    <tr>
                                        <td>Timeslot</td>
                                        <td>{{$data->time_slot_name}}</td>
                                    </tr>

                                    <tr>
                                        <td>Pickup Address</td>
                                        <td>{{$data->address_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>Pickup</td>
                                    </tr>
                                   <tr>
                                        <td>Waive Delivery</td>
                                        @if($data->waver_delivery)
                                        <td>Yes</td>
                                        @else
                                        <td>No</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>Phase/Screen</td>
                                        @if($data->phase)
                                        <td>{{$data->phase}}</td>
                                        @else
                                        <td>

                                        </td>
                                        @endif
                                    </tr>

                                     <tr>
                                        <td>Waive Delivery Date</td>
                                        @if($data->DW_when)
                                        <td>{{$data->DW_when}}</td>
                                        @else
                                        <td>

                                        </td>
                                        @endif
                                    </tr>

                                     <tr>
                                        <td>Waive Delivery By Whom</td>
                                        @if($data->DW_who)
                                        <td>{{$data->order_DW_who}}</td>
                                        @else
                                        <td>

                                        </td>
                                        @endif
                                    </tr>

                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                                    
                                                    <th width="25%">User</th>
                                                    <th width="25%">Role</th>
                                                    <th width="25%">Status Name</th>
                                                    <th width="25%">Timestamp</th>
                                                    
                                                </tr>
                                            </thead>
                                            @if($histories)
                                                <tbody>
                                                    @foreach($histories as $value)
                                                    
                                                        <tr>
                                                            <th>{{ $value->user_name }} </th>
                                                            <th>{{ $value->role_name }} </th>
                                                            <th>{{ $value->status_name }} </th>
                                                            <td>{{ $value->created_at }}</td>
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
