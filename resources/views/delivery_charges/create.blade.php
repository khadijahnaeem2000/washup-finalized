@extends('layouts.master')
@section('title','Delivery charge')
@section('content')
    @include( '../sweet_script')   
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('delivery_charges.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'delivery_charges.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('order_amount','Order Amount <span class="text-danger">*</span>')) !!}
                               {{ Form::number('order_amount', null, array('placeholder' => 'Enter order amount','class' => 'form-control','autofocus' => '','required','min'=>'1'  )) }}
                                @if ($errors->has('order_amount'))  
                                    {!! "<span class='span_danger'>". $errors->first('order_amount')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_charges','Delivery charges <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('delivery_charges', null, array('placeholder' => 'Enter delivery charges','class' => 'form-control','required','min'=>1)) !!}
                                    @if ($errors->has('delivery_charges'))  
                                        {!! "<span class='span_danger'>". $errors->first('delivery_charges')."</span>"!!} 
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
            <!--end::Card-->
        </div>
    </div>
@endsection
