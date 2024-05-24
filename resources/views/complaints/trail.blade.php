@extends('layouts.master')
@section('title','Complaint')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <!-- <div class="card card-custom gutter-b example example-compact">
                
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
                                        <td width="50%">Complaint No</td>
                                        <td>{{$data->id}}</td>
                                    </tr>
                                    <tr>
                                        <td >Order No</td>
                                        <td>{{$data->order_id}}</td>
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
                                   

                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

                                                    
            @if($histories)
                <div class="card card-custom gutter-b example example-compact d-print-none">
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Show complaint Trail(Order# {{$data->order_id}}) </h3>
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
