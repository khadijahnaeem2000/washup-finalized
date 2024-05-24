@extends('layouts.master')
@section('title','Addon Rate List')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('addon_rate_lists.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'addon_rate_lists.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('wash_house_id','Wash House <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('wash_house_id', $wash_houses,null, array('class' => 'form-control','onchange'=>'fetch_addons(this.value)','autofocus' => '')) !!}
                                    @if ($errors->has('wash_house_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('wash_house_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('addon_id','Addon <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('addon_id', [],null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('addon_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('addon_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('rate','Rate <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('rate', null, array('placeholder' => 'Enter rate','class' => 'form-control','required','min'=>0)) }}
                                    @if ($errors->has('rate'))  
                                        {!! "<span class='span_danger'>". $errors->first('rate')."</span>"!!} 
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
    <script>

        // calling fetch addon functions
        $(document).ready(function(){
            var wash_house_id = document.getElementById('wash_house_id').value;  
            fetch_addons(wash_house_id)
        });


        // fetching addon list using wash-house  - id   
        function fetch_addons(wash_house_id) {
            console.log(wash_house_id);
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_addon_lists') }}",
                method: 'POST',
                data: {wash_house_id:wash_house_id, _token:token},
                success: function(res) {
                    console.log(res);
                    $("select[name='addon_id'").html('');
                    $("select[name='addon_id'").html(res.options);
                }
            });
        }
    </script>

@endsection
