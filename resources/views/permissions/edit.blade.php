
@extends('layouts.master')
@section('title','Permissions')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                    <div class="card-toolbar">
                        <div class="example-tools justify-content-center">
                        </div>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($permission, ['method' => 'PATCH','id'=>'form','route' => ['permissions.update', $permission->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12">
                              {!! Html::decode(Form::label('name','Permission Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'permission name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <!-- <button type="reset" class="btn btn-secondary">Reset</button> -->
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>
@endsection
