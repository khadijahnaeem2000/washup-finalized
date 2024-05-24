
@extends('layouts.master')
@section('title','Time Slots')
@section('content')
@include( '../sweet_script')
  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('time_slots.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                 {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['time_slots.update', $data->id]]) !!}
                   
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Time Slot Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter time slot name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('color','Color <span class="text-danger">*</span>')) !!}
                                    {!! Form::color('color', null, array('placeholder' => 'Enter color','class' => 'form-control')) !!}
                                    @if ($errors->has('color'))  
                                        {!! "<span class='span_danger'>". $errors->first('color')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                  

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('start_time','Start Time <span class="text-danger">*</span>')) !!}
                                    {!! Form::time('start_time', null, array('placeholder' => 'Enter start time','class' => 'form-control')) !!}
                                    @if ($errors->has('start_time'))  
                                        {!! "<span class='span_danger'>". $errors->first('start_time')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('end_time','End Time <span class="text-danger">*</span>')) !!}
                                    {!! Form::time('end_time', null, array('placeholder' => 'Enter end time','class' => 'form-control')) !!}
                                    @if ($errors->has('end_time'))  
                                        {!! "<span class='span_danger'>". $errors->first('end_time')."</span>"!!} 
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
