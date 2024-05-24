
@extends('layouts.master')
@section('title','Time Slots')
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
                        <a  href="{{ route('time_slots.index') }}" class="btn btn-primary btn-sm ">
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
                                        <td>Start Time</td>
                                        <td>{{$data->start_time}}</td>
                                    </tr>
                                    <tr>
                                        <td>End Time </td>
                                        <td>{{$data->end_time}}</td>
                                    </tr>

                                    <tr>
                                        <td>Color </td>
                                        <td><div style="width:40px; height:40px; background: #c92626"></div></td>
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
