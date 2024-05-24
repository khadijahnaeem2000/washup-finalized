@extends('layouts.master')
@section('title','Distribution Hub')
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
                        <a  href="{{ route('distribution_hubs.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td>@yield('title') Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address Name</td>
                                        <td>{{$data->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Zone</td>
                                        <td>
                                            @foreach($zones as $value)
                                                <label class="badge badge-success">{{ $value->name }}</label>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Riders</td>
                                        <td>
                                            @foreach($riders as $value)
                                                <label class="badge badge-info">{{ $value->name }}</label>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>User</td>
                                        <td>
                                            @foreach($users as $value)
                                                <label class="badge badge-success">{{ $value->name }}</label>
                                            @endforeach
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Timeslot</td>
                                        <td>
                                            @foreach($time_slots as $value)
                                                <label class="badge badge-success">{{ $value->start_time }} - {{ $value->end_time }}</label>
                                            @endforeach
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Working Days</td>
                                        <td>
                                            @foreach($days as $key=> $value)
                                                <label class="badge badge-success">{{ $value->name }} </label>
                                            @endforeach
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
