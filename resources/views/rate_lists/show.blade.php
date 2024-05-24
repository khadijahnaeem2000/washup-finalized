
@extends('layouts.master')
@section('title','Rate Lists')
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
                        <a  href="{{ route('rate_lists.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td width="50%">Item Name</td>
                                        <td>{{$data->item_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Name</td>
                                        <td>{{$data->service_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Wash house Name </td>
                                        <td>{{$data->wash_house_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Rate </td>
                                        <td>{{$data->rate}}</td>
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
