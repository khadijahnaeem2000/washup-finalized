@extends('layouts.master')
@section('title','Users')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Create New @yield('title')</h3>
                    <div class="card-toolbar">
                        <div class="example-tools justify-content-center">
                        </div>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'users.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12">
                              {!! Html::decode(Form::label('name','Full Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter full name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif

                            </div>
                        </div>
                  

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('email','Email <span class="text-danger">*</span>')) !!}
                                    {!! Form::text('email', null, array('placeholder' => 'Enter valid mail','class' => 'form-control')) !!}
                                    @if ($errors->has('email'))  
                                        {!! "<span class='span_danger'>". $errors->first('email')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('password','Password <span class="text-danger">*</span>')) !!}
                                    {!! Form::password('password', array('placeholder' => 'Enter password','class' => 'form-control')) !!}
                                    @if ($errors->has('password'))  
                                        {!! "<span class='span_danger'>". $errors->first('email')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('roles[]','Roles <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('roles[]', $roles,[], array('class' => 'form-control')) !!}
                                    @if ($errors->has('roles'))  
                                        {!! "<span class='span_danger'>". $errors->first('roles')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Contact No')) !!}
                                    {!! Form::text('contact_no', null, array('placeholder' => 'Enter contact no','class' => 'form-control')) !!}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-10">
                                {!! Html::decode(Form::label('description','Address')) !!}
                                {!! Form::textarea('description', null, array('placeholder' => 'Enter Address','rows'=>5, 'class' => 'form-control')) !!}
                                @if ($errors->has('description'))  
                                    {!! "<span class='span_danger'>". $errors->first('description')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-2" style="margin-top: 20px">
                                <div class="image-input image-input-outline" id="kt_profile_avatar">
                                    <img id="blah" src="{{ asset('uploads/users/no_image.png') }}" class="image-input-wrapper" alt="your image"/>
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
