@extends('layouts.master')
@section('title','Vehicle Type')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('vehicle_types.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['vehicle_types.update', $data->id]]) !!}
                   
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Vechile Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter vehicle name','class' => 'form-control','autofocus' => '','required'=>'true'  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('hanger','Hanging Capability <span class="text-danger">*</span>')) !!}
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            {!! Form::checkbox('hanger',1,$data->hanger,  array('class' => 'form-control')) !!}
                                            <span></span>
                                        </label>
                                    </span>
                                
                                    @if ($errors->has('hanger'))  
                                        {!! "<span class='span_danger'>". $errors->first('hanger')."</span>"!!} 
                                    @endif
                                </div>
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
