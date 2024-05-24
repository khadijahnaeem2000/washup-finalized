
@extends('layouts.master')
@section('title','Holidays')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('holidays.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'holidays.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Holiday  Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter holiday name','class' => 'form-control','autofocus' => '','required'  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('holiday_date','Holiday Date <span class="text-danger">*</span>')) !!}
                                    {{ Form::text('holiday_date', null, array('placeholder' => 'yyyy-mm-dd','class' => 'form-control dpicker','readonly'=>'true','required')) }}
                                    @if ($errors->has('holiday_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('holiday_date')."</span>"!!} 
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

    
    <script type="text/javascript">
         $('.dpicker').datepicker({ 
                startDate: new Date(),
                format: 'yyyy-mm-dd',
            });
    </script>


@endsection
