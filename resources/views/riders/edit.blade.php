@extends('layouts.master')
@section('title','Rider')
@section('content')
@include( '../sweet_script')

    <script>
        var check = 0;
        var p_name = "primary";
    </script>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                    <div class="card-toolbar">
                        <div class="example-tools justify-content-center">
                        </div>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['riders.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Full Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter full name','class' => 'form-control','autofocus' => '','required'=>'true','minlength'=>'3')) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif

                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('status','Attendance<span class="text-danger">*</span>')) !!}
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            {!! Form::checkbox('status',1,$data->status,  array('class' => 'form-control')) !!}
                                            <span></span>
                                        </label>
                                    </span>
                                
                                    @if ($errors->has('status'))  
                                        {!! "<span class='span_danger'>". $errors->first('status')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('username','Username <span class="text-danger">*</span>')) !!}
                                    {!! Form::text('username', null, array('placeholder' => 'Enter  username','class' => 'form-control','required'=>'true','minlength'=>'3')) !!}
                                    @if ($errors->has('username'))  
                                        {!! "<span class='span_danger'>". $errors->first('username')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('password','Password <span class="text-danger">*</span>')) !!}
                                    {!! Form::password('password', array('placeholder' => 'Enter password','class' => 'form-control')) !!}
                                    @if ($errors->has('password'))  
                                        {!! "<span class='span_danger'>". $errors->first('password')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('cnic_no','CNIC No <span class="text-danger">*</span>')) !!}
                                    {!! Form::text('cnic_no', null, array('placeholder' => 'Enter cnic no','class' => 'form-control','required'=>'true','minlength'=>'13')) !!}
                                    @if ($errors->has('cnic_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('cnic_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Contact No <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('contact_no',  null,array('placeholder' => 'Enter contact no','class' => 'form-control','required'=>'true')) !!}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('max_loc','Max: Locations <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('max_loc', null, array('placeholder' => 'Enter maximum location','class' => 'form-control','required'=>'true','min'=>'1')) !!}
                                    @if ($errors->has('max_loc'))  
                                        {!! "<span class='span_danger'>". $errors->first('max_loc')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('color_code','Color ')) !!}
                                    {!! Form::color('color_code',  null,array('placeholder' => 'Enter color','class' => 'form-control','required'=>'true')) !!}
                                    @if ($errors->has('color_code'))  
                                        {!! "<span class='span_danger'>". $errors->first('color_code')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('vehicle_type_id','Vehicle Type')) !!}
                                    {!! Form::select('vehicle_type_id', $vehicle_types,null, array('class' => 'form-control','required'=>'true')) !!}
                                    @if ($errors->has('vehicle_type_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('vehicle_type_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('vehicle_reg_no','Vehicle Registration# <span class="text-danger">*</span>')) !!}
                                    {!! Form::text('vehicle_reg_no', null, array('placeholder' => 'Enter registration no','class' => 'form-control','required'=>'true')) !!}
                                    @if ($errors->has('vehicle_reg_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('vehicle_reg_no')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                               <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('rider_incentives','Rider Compensation')) !!}
                                    {!! Form::select('rider_incentives', $rider_incentives,null, array('class' => 'form-control','required'=>'true')) !!}
                                    @if ($errors->has('rider_incentives'))  
                                        {!! "<span class='span_danger'>". $errors->first('rider_incentives')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('max_route','Max: Route <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('max_route', null, array('placeholder' => 'Enter route location','class' => 'form-control','required'=>'true','min'=>'1')) !!}
                                    @if ($errors->has('max_route'))  
                                        {!! "<span class='span_danger'>". $errors->first('max_route')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('max_drop_weight','Max: Drop Weight <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('max_drop_weight', null, array('placeholder' => 'Enter  max: drop weight in KG','class' => 'form-control','required'=>'true','min'=>'1')) !!}
                                    @if ($errors->has('max_drop_weight'))  
                                        {!! "<span class='span_danger'>". $errors->first('max_route')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('max_pick','Max: Pickup <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('max_pick', null, array('placeholder' => 'Enter pick location','class' => 'form-control','required'=>'true','min'=>'1')) !!}
                                    @if ($errors->has('max_pick'))  
                                        {!! "<span class='span_danger'>". $errors->first('max_pick')."</span>"!!} 
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('max_drop_size','Max: Drop Size <span class="text-danger">*</span>')) !!}
                                    {!! Form::number('max_drop_size', null, array('placeholder' => 'Enter drop size','class' => 'form-control','required'=>'true','min'=>'1')) !!}
                                    @if ($errors->has('max_drop_size'))  
                                        {!! "<span class='span_danger'>". $errors->first('max_drop_size')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-10">
                                {!! Html::decode(Form::label('address','Address ')) !!}
                                {!! Form::textarea('address', null, array('placeholder' => 'Rider Address','rows'=>5, 'class' => 'form-control')) !!}
                                @if ($errors->has('address'))  
                                    {!! "<span class='span_danger'>". $errors->first('address')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-2" style="margin-top: 20px">
                                <div class="image-input image-input-outline" id="kt_profile_avatar">
                                    @if($data->image)
                                        <img id="blah" src="{{ asset('uploads/riders/'.$data->image) }}" class="image-input-wrapper" alt="your image"/></center>
                                    @else
                                        <img id="blah" src="{{ asset('uploads/no_image.png') }}" class="image-input-wrapper" alt="your image"/></center>
                                    @endif
                                    @if ($errors->has('image'))  
                                        {!! "<span class='span_danger'>". $errors->first('image')."</span>"!!} 
                                    @endif
                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Image">
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        {!! Form::file('image', array('id'=>'exampleInputFile','accept'=>'.png, .jpg, .jpeg')) !!}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <h4 class="card-title">Zones</h4> 
                        @if ($errors->has('zone'))  
                                {!! "<span class='span_danger'>". $errors->first('zone')."</span>"!!} 
                        @endif
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="zone_table" class="table">
                                        <thead>
                                            <tr>
                                                <th width="75%">Zone Name </th>
                                                <th width="20%">Priority </th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($selected_zones)){
                                                    foreach($selected_zones as $key => $value){ ?>
                                                    <script type="text/javascript">
                                                        $rowno=$("#zone_table tr").length;
                                                      
                                                        <?php if ($value->priority == 0) {  ?>
                                                                p_name = "Secondary";
                                                                check =0;
                                                        <?php } else { ?>
                                                                p_name = "Primary";
                                                                check =1;
                                                        <?php }?>
                                                            $rowno=$rowno+1;
                                                            $("#zone_table tr:last").after("<tr id='zone_table_row"+$rowno+"'>"+
                                                                        "<td> " +
                                                                            '{!! Form::select("zone[]", $zones,$value->zone_id, array("class"=> "form-control")) !!}'+
                                                                        "</td>"+
                                                                        "<td> " +
                                                                            '<input type="hidden" class="form-control" data-id="'+$rowno+'" value="<?php echo $value->priority?>" name="priority[]" id ="priority['+$rowno+']" />'+
                                                                            '<b>'+p_name+'</b>'+
                                                                        "</td>"+
                                                                        "<td  width='40px'>"+
                                                                            "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_zone_row('zone_table_row"+$rowno+"',"+$rowno+")>"+
                                                                        "</td>"+
                                                            "</tr>");
                                                    </script>
                                            <?php } }?>
                                        <tbody>
                                    </table>
                                </div>

                                <table id="" class="table">
                                    <tbody>
                                        <tr>
                                            <td colspan="6" style="text-align:right"> Add New Row</td>
                                            <td width="5%"><input class="btn btn-success btn-sm" type="button" onclick="add_zone_row();" value="+"></td>
                                        </tr>
                                    <tbody>
                                </table>
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


        function get_last_id(){
            var last_id = 1;
            var all_ids = $("input[name='priority[]']")
              .map(function(){return $(this).attr('data-id');}).get();

            for (var i = 0; i < all_ids.length; i++) 
            {
                console.log(all_ids[i]);
                last_id = all_ids[i];
                
            }
            return last_id;
        }

        function has_arr_primary_key()
        {
            var all_priority = $("input[name='priority[]']")
              .map(function(){return $(this).val();}).get();

            for (var i = 0; i < all_priority.length; i++) 
            {
                // console.log(all_priority[i]);
                if(all_priority[i] == 1)
                {
                    // console.log("yes it has primary zone");
                    return true;
                }
            }
            
            console.log("no it has primary zone");
            return false;

        }

        function add_zone_row(){
            var last_id     = parseInt(get_last_id());


            var prmry_key   = has_arr_primary_key();
            $rowno          = $("#zone_table tr").length;
            // alert($rowno);
            if(!prmry_key){
                
                $rowno      = last_id+1;
                $("#zone_table tr:last").after("<tr id='zone_table_row"+$rowno+"'>"+
                    "<td> " +
                    '{!! Form::select("zone[]", $zones,null, array("class"=> "form-control")) !!}'+
                    "</td>"+
                    "<td> " +
                        '<input type="hidden" class="form-control" value="1"  data-id = '+$rowno+' name="priority[]" id ="priority['+$rowno+']" />'+
                        '<b>Primary</b>'+
                    "</td>"+

                    "<td  width='40px'>"+
                        "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_zone_row('zone_table_row"+$rowno+"',"+$rowno+")>"+
                    "</td>"+
                "</tr>");
            }else{
                $rowno      = last_id+1;
                $("#zone_table tr:last").after("<tr id='zone_table_row"+$rowno+"'>"+
                    "<td> " +
                    '{!! Form::select("zone[]", $zones,null, array("class"=> "form-control")) !!}'+
                    "</td>"+
                    "<td> " +
                        '<input type="hidden" class="form-control" value="0" data-id = '+$rowno+' name="priority[]" id ="priority['+$rowno+']"/>'+
                        '<b>Secondary</b>'+
                    "</td>"+

                    "<td  width='40px'>"+
                        "<input class='btn btn-danger btn-sm' type='button'  value='-' onclick=delete_zone_row('zone_table_row"+$rowno+"',"+$rowno+")>"+
                    "</td>"+
                "</tr>");
            }
        }
        function delete_zone_row(rowno,rno)
        {
            $('#'+rowno).remove();
        }
    </script>
@endsection
