@extends('layouts.master')
@section('title','Wash House')
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
                        <a  href="{{ route('wash_houses.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td width="50%">@yield('title') Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Capacity</td>
                                        <td>{{$data->capacity}}</td>
                                    </tr>
                                    <tr>
                                        <td>Zone</td>
                                        <td>
                                            @foreach($zones as $value)
                                                <label class="badge badge-success">{{ $value->name }}</label><br>
                                            @endforeach
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>User</td>
                                        <td>
                                            @foreach($users as $value)
                                                <label class="badge badge-success">{{ $value->name }} </label><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Distribution Hub</td>
                                        <td>
                                            @foreach($hubs as $value)
                                                <label class="badge badge-info">{{ $value->name }}</label><br>
                                            @endforeach
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Services</td>
                                        <td>
                                            @foreach($services as $value)
                                                <label class="badge badge-success">{{ $value->name }} </label><br>
                                            @endforeach
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Addons</td>
                                        <td>
                                            @foreach($addons as $value)
                                                <label class="badge badge-success">{{ $value->name }} </label><br>
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
