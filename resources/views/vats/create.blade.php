@extends('layouts.master')
@section('title','VAT')
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
                {!! Form::open(array('route' => 'vats.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('vat','VAT <span class="text-danger">*</span>')) !!}
                               {{ Form::number('vat', null, array('placeholder' => 'Enter VAT %','class' => 'form-control','autofocus' => '','required','min'=>'0'  )) }}
                                @if ($errors->has('vat'))  
                                    {!! "<span class='span_danger'>". $errors->first('vat')."</span>"!!} 
                                @endif
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
