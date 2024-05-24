@extends('layouts.master')
@section('title','Rider')
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
                        <a  href="{{ route('riders.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td style="width:50%">Full Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Username </td>
                                        <td>{{$data->username}}</td>
                                    </tr>
                                    <tr>
                                        <td>CNIC No </td>
                                        <td>{{$data->cnic_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Contact No </td>
                                        <td>{{$data->contact_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Max: Locations </td>
                                        <td>{{$data->max_loc}}</td>
                                    </tr>
                                    <tr>
                                        <td>Max Route</td>
                                        <td>{{$data->max_route}}</td>
                                    </tr>
                                    <tr>
                                        <td>Max Pickup </td>
                                        <td>{{$data->max_pick}}</td>
                                    </tr>
                                    <tr>
                                        <td>Max Drop weight </td>
                                        <td>{{$data->max_drop_weight}} Kg</td>
                                    </tr>
                                    <tr>
                                        <td>Color </td>
                                        <td><div style="width:40px; height:40px; background:{!!$data->color_code!!}"></div></td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Type </td>
                                        <td>{{$data->vehicle_type_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Registration </td>
                                        <td>{{$data->vehicle_reg_no}}</td>
                                    </tr>
                                    
                                     <tr>
                                        <td>Rider Compensation</td>
                                        <td>{{$data->rider_incentives_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Zone</td>
                                        <td>
                                        @if($zones)
                                            @foreach($zones as $value)
                                                <label class="badge badge-success">{{ $value->name }} </label>
                                                
                                                    <?php if( $value->priority == 1 ){?>
                                                        <label class="badge badge-primary"> Primary</label>
                                                    <?php }else{?>
                                                        <label class="badge badge-info"> Secondary</label>

                                                    <?php }?>
                                                 
                                               
                                                <br>
                                            @endforeach
                                        @else
                                            <label class="badge badge-info">{{ "N/A" }} </label>
                                        @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Address </td>
                                        <td>{{$data->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Rider  </td>
                                        <td >
                                            @if($data->image)
                                                <img src="{{ asset('uploads/riders/'.$data->image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);" height="64" width="64">
                                            @else
                                                <img src="{{ asset('uploads/no_image.png') }}" alt="users view avatar" class="users-avatar-shadow rounded-circle"  style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"height="64" width="64">
                                           @endif
                                        </td>
                                    </tr>
                                   
                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
