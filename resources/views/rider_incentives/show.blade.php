@extends('layouts.master')
@section('title','Rider Compensation')
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
                        <a  href="{{ route('rider_incentives.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td style="width:50%">Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickup Rate</td>
                                        <td>{{$data->pickup_rate}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickdrop Rate</td>
                                        <td>{{$data->pickdrop_rate}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kilometer Rate</td>
                                        <td>{{$data->kilometer}}</td>
                                    </tr>
                                   <tr>
                                        <td>Status</td>
                                        @if($data->status === 1)
                                        <td>Active</td>
                                        @else 
                                        <td>InActive</td>
                                        @endif
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
