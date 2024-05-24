@extends('layouts.master')
@section('title','Item')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('items.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                
                <!--begin::Form-->
                {!! Form::open(array('route' => 'items.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Item Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter item name','class' => 'form-control','autofocus' => '', 'required','minlength'=>'3'))
                                }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('short_name','Item Short Name <span class="text-danger">*</span>')) !!}
                                {!! Form::text('short_name', null, array('placeholder' => 'Item Short Name','class' => 'form-control', 'required','minlength'=>'2')) !!}
                                @if ($errors->has('short_name')) 
                                    {!! "<span class='span_danger'>". $errors->first('short_name')."</span>"!!} 
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('description','Description ')) !!}
                                {!! Form::textarea('description', null, array('placeholder' => 'Item description','rows'=>5, 'class' => 'form-control')) !!}
                                @if ($errors->has('description'))  
                                    {!! "<span class='span_danger'>". $errors->first('description')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-top: 20px">
                                <div class="image-input image-input-outline" id="kt_profile_avatar">
                                    <img id="blah" src="{{ asset('uploads/no_image.png') }}" class="image-input-wrapper" alt="your image"/>
                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Image">
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        {!! Form::file('image', array('id'=>'exampleInputFile','accept'=>'.png, .jpg, .jpeg')) !!}
                                    </label>
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

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#exampleInputFile").change(function() {
            readURL(this);
        });
    </script>
@endsection
