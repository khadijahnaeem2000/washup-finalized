@extends('layouts.master')
@section('title','Rate Lists')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('rate_lists.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'rate_lists.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('wash_house_id','Wash House <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('wash_house_id', $wash_houses,null, array('class' => 'form-control','onchange'=>'fetch_services(this.value)','autofocus' => '')) !!}
                                    @if ($errors->has('wash_house_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('wash_house_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('service_id','Service <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('service_id', [],null, array('class' => 'form-control','onchange'=>'fetch_items(this.value)')) !!}
                                    @if ($errors->has('service_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('service_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('item_id','Items <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('item_id', [],null, array('class' => 'form-control','required')) !!}
                                    @if ($errors->has('item_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('item_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('rate','Rate <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('rate', null, array('placeholder' => 'Enter rate','class' => 'form-control','required')) }}
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

        // calling fetch service functions
        $(document).ready(function(){
            var wash_house_id = document.getElementById('wash_house_id').value;  
            fetch_services(wash_house_id)
        });


        // fetching services list using wash-house  - id   
        function fetch_services(wash_house_id) {
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_services') }}",
                method: 'POST',
                data: {wash_house_id:wash_house_id, _token:token},
                success: function(res) {
                    $("select[name='service_id'").html('');
                    $("select[name='service_id'").html(res.options);
                    var service_id = document.getElementById('service_id').value;  
                    fetch_items(service_id)
                }
            });
        }

        // fetching items list using service  - id
        function fetch_items(service_id) {
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_items_list') }}",
                method: 'POST',
                data: {service_id:service_id, _token:token},
                success: function(res) {
                    $("select[name='item_id'").html('');
                    $("select[name='item_id'").html(res.options);
                }
            });
        }
    </script>

@endsection
