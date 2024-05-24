@extends('layouts.master')
@section('title','Rider Compensation')
@section('content')
@include( '../sweet_script')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=places"></script>  
    <style type="text/css">
        #map_canvas {
            width: 100%;
            height: 500px;
        }
        #current {
            padding-top: 25px;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title') </h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('rider_incentives.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'rider_incentives.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('name',' Name <span class="text-danger">*</span>')) !!}
                                    {{ Form::text('name', null, array('placeholder' => 'Enter name','class' => 'form-control','autofocus' => '','required'=>'true','minlength'=>'3')) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('pickup_rate','Pickup Rate ')) !!}
                                    {{ Form::number('pickup_rate', null, array('placeholder' => 'Enter pickup rate','class' => 'form-control')) }}
                                    @if ($errors->has('pickup_rate'))  
                                        {!! "<span class='span_danger'>". $errors->first('pickup_rate')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('pickdrop_rate','Pickdrop Rate')) !!}
                                    {{ Form::number('pickdrop_rate', null, array('placeholder' => 'Enter pickdrop rate','class' => 'form-control')) }}
                                    @if ($errors->has('pickdrop_rate'))  
                                        {!! "<span class='span_danger'>". $errors->first('pickdrop_rate')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('kilometer','Kilometer rate')) !!}
                                    {{ Form::number('kilometer', null, array('placeholder' => 'Enter kilometer rate','class' => 'form-control')) }}
                                    @if ($errors->has('kilometer'))  
                                        {!! "<span class='span_danger'>". $errors->first('kilometer')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                  <div class="form-group">
                                    {!! Html::decode(Form::label('status','Status <span class="text-danger">*</span>')) !!}
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            {!! Form::checkbox('status',1,true,  array('class' => 'form-control')) !!}
                                            <span></span>
                                        </label>
                                    </span>
                                
                                    @if ($errors->has('status'))  
                                        {!! "<span class='span_danger'>". $errors->first('status')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                       
                    </div>


                    <div class="card-footer">
                        <div class="row">
                            
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>

 


@endsection

