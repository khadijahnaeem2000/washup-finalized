@extends('layouts.master')
@section('title','Service')
@section('content')

@include( '../sweet_script')

    <style>
    .imgStyle{
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            margin: 5px;
            background: #3495eb;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('services.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['services.update', $data->id]]) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                    {!! Form::hidden('status', 1, array('class' => 'form-control')) !!}

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('name','Service  Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter service name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('unit_id','Unit <span class="text-danger">*</span>')) !!}
                                {!! Form::select('unit_id', ['0' =>'--- Select Unit ---']+$units,null, array('class' => 'form-control','onchange'=>'show_items(this.value)')) !!}
                                @if ($errors->has('unit_id'))  
                                    {!! "<span class='span_danger'>". $errors->first('unit_id')."</span>"!!} 
                                @endif
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3 col-md-3 col-sm-5 col-xs-5">
                                {!! Html::decode(Form::label('qty','Unit Quantity ')) !!}
                                {{ Form::number('qty', null, array('placeholder' => 'Enter unit quantity','class' => 'form-control', 'min'=>'1')) }}
                                @if ($errors->has('qty'))  
                                    {!! "<span class='span_danger'>". $errors->first('qty')."</span>"!!} 
                                @endif
                            </div> 
                            <div class="col-lg-3 col-md-3 col-sm-5 col-xs-5">
                                {!! Html::decode(Form::label('order_number','Order Number <span class="text-danger">*</span>')) !!}
                                {{ Form::number('order_number', null, array('placeholder' => 'Enter order number','class' => 'form-control', 'required')) }}
                                @if ($errors->has('order_number'))  
                                    {!! "<span class='span_danger'>". $errors->first('order_number')."</span>"!!} 
                                @endif
                            </div> 
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('rate','Rate')) !!}
                                {!! Form::number('rate', null, array('placeholder' => 'Enter rate','class' => 'form-control', 'min'=>'0')) !!}
                                @if ($errors->has('rate'))  
                                    {!! "<span class='span_danger'>". $errors->first('rate')."</span>"!!} 
                                @endif
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('hanger','Hanger <span class="text-danger">*</span>')) !!}
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <label>
                                            {!! Form::checkbox('hanger',1,$data->hanger,  array('class' => 'form-control')) !!}
                                            <span></span>
                                        </label>
                                    </span>
                                
                                    @if ($errors->has('hanger'))  
                                        {!! "<span class='span_danger'>". $errors->first('hanger')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        
                            
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-8">
                                {!! Html::decode(Form::label('description','Description ')) !!}
                                {!! Form::textarea('description', null, array('placeholder' => 'Item description','rows'=>5, 'class' => 'form-control')) !!}
                                @if ($errors->has('description'))  
                                    {!! "<span class='span_danger'>". $errors->first('description')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('image','Mobile Image')) !!}
                                <div class="image-input image-input-outline" >
                                    @if($data->image)
                                        <img id="blah" src="{{ asset('uploads/services/'.$data->image) }}" class="image-input-wrapper imgStyle" alt="your image" /></center>
                                    @else
                                        <img id="blah" src="{{ asset('uploads/no_image.png') }}" class="image-input-wrapper imgStyle" alt="your image"/></center>
                                    @endif
                                    @if ($errors->has('image'))  
                                        {!! "<span class='span_danger'>". $errors->first('image')."</span>"!!} 
                                    @endif

                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" >
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        {!! Form::file('image', array('id'=>'exampleInputFile','accept'=>'.png, .jpg, .jpeg')) !!}
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
                            {!! Html::decode(Form::label('image1','Web Image ')) !!}
                                <div class="image-input image-input-outline" >
                                    @if($data->web_image)
                                        <img id="web_img" src="{{ asset('uploads/services/'.$data->web_image) }}" class="image-input-wrapper imgStyle" alt="your image" style= "margin: 5px;"/></center>
                                    @else
                                        <img id="web_img" src="{{ asset('uploads/no_image.png') }}" class="image-input-wrapper imgStyle" alt="your image"/></center>
                                    @endif
                                    @if ($errors->has('web_image'))  
                                        {!! "<span class='span_danger'>". $errors->first('web_image')."</span>"!!} 
                                    @endif

                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" >
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        {!! Form::file('web_image', array('id'=>'web_image','accept'=>'.png, .jpg, .jpeg')) !!}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-title">
                            <h3 class="card-label">Items</h3>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="myTable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="40%">Item</th>
                                                <th width="40%">Addon</th>
                                                <th width="10%">Rate</th>
                                                <th width="5%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                     
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
       $(document).ready(function(){
        var unit_id = document.getElementById('unit_id').value;  
        //    alert("adf");
           show_items(unit_id)
       });
        function show_items($id){
            $("#myTable .tb_main").remove();
            if($id==2){
                $rowno=-1;
                $('#rate').val(0);
                $('#rate').attr("readonly", "true");
               
                <?php  foreach($items as $key => $value){ 
                    
                    ?>


                    
                    $rowno=$rowno+1;

                    $("#myTable ").append(
                        "<tr class='tb_main' id='row"+$rowno+"'>"+
                            "<td>  "+
                                '<?php echo ($key+1) ;?>'+
                            "</td>"+
                            "<td>  "+
                                '{!! Form::hidden("items[]", $value->item_id, array("class"=> "form-control","readonly"=>"true")) !!}'+
                                '<?php echo $value->item_name ;?>'+
                            "</td>"+
                          
                            "<td> " +
                                '<?php
                                    foreach($addons as $addonKey => $addonvalue){ 
                                        $checkBox = null;
                                    if(!empty($selected_add))
                                    {
                                        if(array_key_exists($value->item_id, $selected_add)){
                                            if(array_key_exists($addonvalue->addon_id, $selected_add[$value->item_id]))
                                            {
                                                $checkBox = true;
                                            }
                                        }
                                      
                                    }
                                    ?>'+
                                    '<div class="checkbox-list">'+
                                        '<label class="checkbox">'+ 
                                        '{!! Form::checkbox("item_addons[$key][$addonvalue->addon_id]", null,$checkBox, array("class" => "form-control")) !!}'+
                                        '<span></span><?php echo $addonvalue->addon_name?></label>'+
                                    '</div>' +                                     
                                '<?php }?>'+
                            "</td>"+
                                    
                                  
                               
                            "</td>"+
                            '<?php 
                                $check = 0;
                                foreach($selected_items as $item_key => $item_value){
                                    if($value->item_id == $item_value->item_id){ 
                                        $check = 1;
                                        break;
                                        }
                                    }
                                if($check == 1){
                            ?>'+
                                "<td> " +
                                    '{!! Form::number("item_rates[]", $item_value->item_rate, array("placeholder" => "Rate","class" => "form-control")) !!}'+
                                "</td>"+

                                "<td> " +
                                    '<span class="switch switch-outline switch-icon switch-primary">'+
                                        '<label>'+
                                            '{!! Form::checkbox("item_status[$key]", 1, true,array("class" => "form-control")) !!}'+
                                            '<span></span>'+
                                        '</label>'+
                                    '</span>'+
                                    
                                "</td>"+
                                  
                                '<?php  }else{ ?>'+
                                "<td> " +
                                    '{!! Form::number("item_rates[]", null, array("placeholder" => "Rate","class" => "form-control")) !!}'+
                                "</td>"+

                                "<td> " +
                                    '<span class="switch switch-outline switch-icon switch-primary">'+
                                        '<label>'+
                                            '{!! Form::checkbox("item_status[$key]", 0, null,array("class" => "form-control")) !!}'+
                                            '<span></span>'+
                                        '</label>'+
                                    '</span>'+
                                    
                                "</td>"+

                                '<?php }?>'+
                        "</tr>"
                    );
                <?php }?>
            }else if($id==1 || $id ==3){
                $rowno=-1;
                $('#rate').removeAttr("readonly");

                <?php  $iRate = 0 ; foreach($items as $key => $value){ ?>
                    
                    $rowno=$rowno+1;

                    $("#myTable ").append(
                        "<tr class='tb_main' id='row"+$rowno+"'>"+
                            "<td>  "+
                                '<?php echo ($key+1) ;?>'+
                            "</td>"+
                            "<td>  "+
                                '{!! Form::hidden("items[]", $value->item_id, array("class"=> "form-control","readonly"=>"true")) !!}'+
                                '<?php echo $value->item_name ;?>'+
                            "</td>"+
                          
                            "<td> " +
                                '<?php
                                    
                                    foreach($addons as $addonKey => $addonvalue){ 
                                        $checkBox = null;
                                    if(!empty($selected_add))
                                    {
                                        if(array_key_exists($value->item_id, $selected_add)){
                                            if(array_key_exists($addonvalue->addon_id, $selected_add[$value->item_id]))
                                            {
                                                $checkBox = true;
                                            }
                                        }
                                      
                                    }
                                    ?>'+
                                    '<div class="checkbox-list">'+
                                        '<label class="checkbox">'+ 
                                        '{!! Form::checkbox("item_addons[$key][$addonvalue->addon_id]", null,$checkBox, array("class" => "form-control")) !!}'+
                                        '<span></span><?php echo $addonvalue->addon_name?></label>'+
                                    '</div>' +                                     
                                '<?php }?>'+
                            "</td>"+
                            '<?php 
                                $check = 0;
                                foreach($selected_items as $item_key => $item_value){
                                    if($value->item_id == $item_value->item_id){ 
                                        $check =1;
                                        break;
                                        }
                                    }
                                if($check == 1){
                            ?>'+
                                "<td> " +
                                    '{!! Form::number("item_rates[]", $iRate, array("placeholder" => "Rate","class" => "form-control","readonly"=>"true")) !!}'+
                                "</td>"+

                                "<td> " +
                                    '<span class="switch switch-outline switch-icon switch-primary">'+
                                        '<label>'+
                                            '{!! Form::checkbox("item_status[$key]", 1, true,array("class" => "form-control")) !!}'+
                                            '<span></span>'+
                                        '</label>'+
                                    '</span>'+
                                    
                                "</td>"+
                                  
                                '<?php  }else{ ?>'+
                                "<td> " +
                                    '{!! Form::number("item_rates[]", 0, array("placeholder" => "Rate","class" => "form-control","readonly"=>"true")) !!}'+
                                "</td>"+

                                "<td> " +
                                    '<span class="switch switch-outline switch-icon switch-primary">'+
                                        '<label>'+
                                            '{!! Form::checkbox("item_status[$key]", 0, false,array("class" => "form-control")) !!}'+
                                            '<span></span>'+
                                        '</label>'+
                                    '</span>'+
                                    
                                "</td>"+

                                '<?php }?>'+
                        "</tr>"
                    );
                <?php }?>

            }
        }
    </script>
    

    <script>
       function readURL(input,img) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $(img).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        $("#exampleInputFile").change(function() {
            // console.log("blah");
          readURL(this,'#blah');
        });

        $("#web_image").change(function() {
            // console.log("web_image");
          readURL(this,'#web_img');
        });
    </script>
@endsection
