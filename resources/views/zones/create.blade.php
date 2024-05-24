@extends('layouts.master')
@section('title','Zone')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('zones.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'zones.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12">
                              {!! Html::decode(Form::label('name','Zone Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter zone name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                        </div>
                  

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('area[]','Area <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('area[]', $areas,null, array('class' => 'form-control','multiple')) !!}
                                    @if ($errors->has('area[]'))  
                                        {!! "<span class='span_danger'>". $errors->first('area[]')."</span>"!!} 
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

