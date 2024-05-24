
@extends('layouts.master')
@section('title','Items')
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
                        <a  href="{{ route('items.index') }}" class="btn btn-primary btn-sm font-weight-bolder">
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
                                        <td>
                                            @if($data->name)
                                                {{$data->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Short Name</td>
                                        <td>
                                            @if($data->short_name)
                                                {{$data->short_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Description</td>
                                        <td>
                                            @if($data->description)
                                                {{$data->description}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Item Pic  </td>
                                        <td >
                                            @if($data->image)
                                                <img src="{{ asset('uploads/items/'.$data->image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle custom_image" >
                                            @else
                                                <img src="{{ asset('uploads/no_image.png') }}" alt="users view avatar" class="users-avatar-shadow rounded-circle custom_image">
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
