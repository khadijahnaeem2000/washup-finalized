@extends('layouts.master')
@section('title','Wash House')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('wash_houses.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['wash_houses.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Wash House Name <span class="text-danger">*</span>')) !!}
                              {{ Form::hidden('id', null, array('class' => 'form-control','id'=>'id')) }}
                               {{ Form::text('name', null, array('placeholder' => 'Enter wash house name','class' => 'form-control','autofocus' => '' ,'required' )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('capacity','Wash House Capacity <span class="text-danger">*</span>')) !!}
                               {{ Form::number('capacity', null, array('placeholder' => 'Enter capacity in pieces','class' => 'form-control','required')) }}
                                @if ($errors->has('capacity'))  
                                    {!! "<span class='span_danger'>". $errors->first('capacity')."</span>"!!} 
                                @endif
                            </div>
                        </div>
                  

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('hub_id','Distribution Hubs <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('hub_id', $hubs,$selectedHubs, array('class' => 'form-control','onchange'=>'fetch_zones(this.value)','required')) !!}
                                    @if ($errors->has('hub_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('hub_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('zone[]','Zone <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('zone[]', $zones,$selectedZones, array('class' => 'form-control','multiple','required')) !!}
                                    @if ($errors->has('zone'))  
                                        {!! "<span class='span_danger'>". $errors->first('zone')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('user[]','Users <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('user[]',$users,$selectedUsers, array('class' => 'form-control','multiple','required')) !!}
                                    @if ($errors->has('user'))  
                                        {!! "<span class='span_danger'>". $errors->first('user')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h3 class="card-title">Services</h3>                    
                        @if ($errors->has('service'))  
                            {!! "<span class='span_danger'>". $errors->first('service')."</span>"!!} 
                        @endif
                        <!-- services -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="myTable" class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:60%">Service</th>
                                                <th style="width:40%">Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                       <?php   $check = 0; ?>
                                            @foreach($services as $key => $value)
                                            <?php 
                                           
                                            foreach($selectedServices as $service_key => $service_value){
                                                if($value->id == $service_value->service_id){ 
                                                    //   echo "value->id: " .$service_value->service_id ;
                                                    $check = 1;
                                                    break;
                                                }else{
                                                    $check =0;
                                                }}
                                            if($check == 1){?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }} 
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("service[]", $value->id, true,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }else{?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("service[]", $value->id, false,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            @endforeach
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <h3 class="card-title">Addons</h3>                    
                        @if ($errors->has('addon'))  
                            {!! "<span class='span_danger'>". $errors->first('addon')."</span>"!!} 
                        @endif
                        <!-- services -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="myTable" class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:60%">Addon</th>
                                                <th style="width:40%">Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                       <?php   $check = 0; ?>
                                            @foreach($addons as $key => $value)
                                            <?php 
                                           
                                            foreach($selectedAddons as $addon_key => $addon_value){
                                                if($value->id == $addon_value->addon_id){ 
                                                    //   echo "value->id: " .$service_value->service_id ;
                                                    $check = 1;
                                                    break;
                                                }else{
                                                    $check =0;
                                                }}
                                            if($check == 1){?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }} 
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("addon[]", $value->id, true,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }else{?>
                                                <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        <span class="switch switch-outline switch-icon switch-primary">
                                                            <label>
                                                                {!! Form::checkbox("addon[]", $value->id, false,array("class" => "form-control")) !!}
                                                                <span></span> 
                                                            </label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            @endforeach
                                        <tbody>
                                    </table>
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

    <script type="text/javascript">
    //    $(document).ready(function(){
    //         var hub_id = document.getElementById('hub_id').value;  
            
    //         console.log("id: " + hub_id);
    //         fetch_zones(hub_id);
    //     });
        function fetch_zones($hub_id){
            var wash_house_id = document.getElementById('id').value;  
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_zones') }}",
                method: 'POST',
                data: {hub_id:$hub_id,wash_house_id:wash_house_id, _token:token},
                success: function(data) {
                    // console.log(data.data);
                    if(data.data){
                        $("select[name='zone\\[\\]']").html(data.data);
                    }else{
                        $("select[name='zone\\[\\]']").html('<option>No Record found- All zone of this Hub, are already assigned</option>');
                    }
                }
            });
        }
        
    </script>


@endsection

