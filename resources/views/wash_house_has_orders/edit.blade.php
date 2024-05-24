@extends('layouts.master')
@section('title','Order detail')
@section('content')
@include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit @yield('title')</h3>
                  
                    <div class="card-toolbar">
                        <a  href="{{ route('orders.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['order_details.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'readonly' => 'true')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                           
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('order_id','Order No <span class="text-danger">*</span>')) !!}
                                {{ Form::text('order_id', null, array('placeholder' => 'Enter order no','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('order_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_id')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('status_id','Status')) !!}
                                    {!! Form::select('status_id', $statuses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('status_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('status_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>


                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                    {{ Form::date('delivery_date', null, array('placeholder' => 'Enter delivery date','class' => 'form-control')) }}
                                    @if ($errors->has('delivery_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('delivery_date')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                  
                        
                        
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('permanent_note','Permanent Note: ')) !!}
                                    {!! Form::textarea('permanent_note', null, array('placeholder' => 'Permanent Note','rows'=>5, 'class' => 'form-control','readonly' => 'true' )) !!}
                                    @if ($errors->has('permanent_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('permanent_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('order_note','Order Note: ')) !!}
                                    {!! Form::textarea('order_note', null, array('placeholder' => 'Order Note','rows'=>5, 'class' => 'form-control')) !!}
                                    @if ($errors->has('order_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-title">
                            <h5 class="card-label">
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        Service Name:
                                    </div>
                                    <div class="col-lg-7 col-md-12 sm-12">
                                        {!! Form::select('service', $services,null, array('class' => 'form-control','id'=>'service')) !!}
                                        @if ($errors->has('service'))  
                                            {!! "<span class='span_danger'>". $errors->first('service')."</span>"!!} 
                                        @endif
                                        
                                    </div>

                                    <div class="col-lg-3 col-md-12 sm-12">
                                        <input class='btn btn-primary btn-block font-weight-bolder' type='button' value='Add new service'  onclick="add_service_div()">
                              
                                    </div>
                                </div>
                            </h5>
                        </div>

                        <div id="service_items">
                            <?php foreach($selected_services as $service_key => $service_value){?>
                                <table class="table" id="table<?php echo $service_value->service_id?>">
                                    <thead>
                                        <tr>
                                            <th colspan="5"><center><?php echo $service_value->service_name?> </center></th>							
                                        </tr>
                                        <tr>
                                            <th  style="width: 25%;">Item</th>
                                            <th  style="width: 10%;">Qty</th>
                                            <th  style="width: 15%;">Addons</th>
                                            <th style="width: 20%;">Image</th>	
                                            <th style="width: 25%;">Note</th>		
                                            <th style="width: 5%;"></th>									
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($selected_items as $item_key => $item_value){
                                            if( $service_value->service_id ==  $item_value->service_id){?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" value="<?php echo $service_value->service_id?>" class="form-control" name="service_id[<?php echo $service_value->service_id?>]"/>
                                                    <select class="form-control item_select" name="item_id[<?php echo $service_value->service_id?>][]" >
                                                        <option value="0"  disabled>---Select Item ---</option>
                                                        @foreach($items as $key => $value)
                                                            @if($value->service_id == $service_value->service_id)
                                                                @if($value->item_id == $item_value->item_id )
                                                                    <option value="{{ $value->item_id }}" selected>{{ $value->item_name }}</option>
                                                                @else
                                                                    <option value="{{ $value->item_id }}">{{ $value->item_name }}</option>
                                                                @endif
                                                            @else
                                                                @continue
                                                            @endif
                                                        @endforeach
                                                            
                                                    </select>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control calc_num" value="<?php echo $item_value->pickup_qty?>" name="pickup_qty[<?php echo $service_value->service_id?>][]" id ="pickup_qty[<?php echo $service_value->service_id?>][]" onchange="calc_value(<?php echo $service_value->service_id?>)"/>
                                                </td>
                                               
                                                <td class="item_addons">
                                                    @foreach($addons as $addon_key => $addon_value)
                                                    
                                                        @if(($item_value->service_id == $addon_value->service_id) && ($item_value->item_id == $addon_value->item_id) )
                                                        <?php  $check = 0;
                                                            if(array_key_exists($addon_value->service_id, $selected_adds))
                                                            {
                                                                if(array_key_exists($addon_value->item_id, $selected_adds[$addon_value->service_id]))
                                                                {
                                                                    if(in_array($addon_value->addon_id, $selected_adds[$addon_value->service_id][$addon_value->item_id] )){
                                                                        $check = true;
                                                                        
                                                                    }
                                                                }
                                                            }
                                                        ?>

                                                            <div class="checkbox-list">
                                                                <label class="checkbox">
                                                                <input type ="checkbox" name="item_addons[<?php echo $service_value->service_id?>][<?php echo $item_value->item_id?>][<?php echo $addon_value->addon_id?>]" <?php echo ($check == true ? 'checked=checked' : '');?> class="form-control" >
                                                                <span></span>{{  $addon_value->addon_name }}</label>
                                                            </div>
                                                        @endif                                                       
                                                    @endforeach
                                                </td>

                                                <td>
                                                    <input type="file" class="form-control" value="c:/passwords.txt" name="item_image[<?php echo $service_value->service_id?>][]" id="item_image" />
                                                    @if($item_value->item_image)
                                                      image uploaded    
                                                    @else
                                                        <!-- <label for="item_image" id="image"> select image</lable> -->
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="<?php echo $item_value->note?>"  placeholder="Note"  name="item_note[<?php echo $service_value->service_id?>][]"/>
                                                </td>
                                                
                                                <td>
                                                    <a href="javascript:void(0);" data-id="<?php echo $service_value->service_id?>" class="btn btn-danger del_row btn-sm btn-clean btn-icon">
                                                        <i class="la la-minus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } }?>
                                    </tbody>
                                </table>
                                <table id="table_summary<?php echo $service_value->service_id?>" class="table">
                                <tbody>
                                    <tr>
                                        <th width="25">
                                            Total Quantity
                                        </th>
                                        <td width="25%">
                                            <input type="number" class="form-control" value="<?php echo $service_value->service_qty?>" name="qty[<?php echo $service_value->service_id?>]" id="qty[<?php echo $service_value->service_id?>]" readonly="true" />
                                        </td>
                                    
                                        <th width="25">
                                            Total Weight
                                        </th>
                                        <td width="25%">
                                            <input type="number" class="form-control" value="<?php echo $service_value->service_weight?>" name="weight[<?php echo $service_value->service_id?>]" />
                                        </td>
                                    </tr>
                               <tbody>
                            </table>

                            <table id="table_tab<?php echo $service_value->service_id?>" class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="4" style="text-align:right"> Add New Row</td>
                                        <td width="5%"> <input class="btn btn-success btn-sm add_row" data-id="<?php echo $service_value->service_id?>" type="button"  value="+"></td>
                                    </tr>
                               <tbody>
                            </table>
                            <?php } ?>
                           
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
      
        var contact_no = document.getElementById('contact_no').value;  
            show_details(contact_no);
        });
        function show_details($contact_no){
            // alert($contact_no);
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetchCustomerDetail') }}",
                method: 'POST',
                data: {contact_no:$contact_no, _token:token},
                success: function(data) {
                    // $('.upload_leter').change(function(){
                    //         var name = document.getElementsByClassName('upload_leter'); 
                    //         if(name){
                    //             $(".upload_leter_main .uploaderform").text(name[0].files[0].name); 
                    //         }
                        
                            
                    //     });
                    // console.log(data.data.id);
                    if(data.data){
                        $('#name').val(data.data.name);
                        $('#customer_id').val(data.data.id);
                        $('#permanent_note').val(data.data.permanent_note);
                        $("#pickup_address_id").html(data.customer_address);
                    }else{
                        $('#name').val('');
                        $('#customer_id').val('');
                        $('#permanent_note').val('');
                        $("#pickup_address_id").html('<option>Please Select Address</option>');
                    }
                    // $("select[name='option_value_id["+rowId+"]'").html(data.options);
                }
            });
        }
    </script>
    <script type="text/javascript">
        var customer_id = document.getElementById('customer_id').value; 
        function add_service_div(){
            var service_id  = document.getElementById('service').value; 
            // console.log("Serivce id: "  + service_id );
            fetch_items(service_id,customer_id);
        }
        
        function fetch_image(e){
            // alert();
            console.log(e.nextElementSibling);
            e.nextElementSibling.innerHTML= " image uploaded";
            // $('#image').html("<i class='fas fa-pencil-alt'></i>  image uploaded");
        }
        
        function fetch_items($service_id,$customer_id){
            // alert($contact_no);
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_items') }}",
                method: 'POST',
                data: {service_id:$service_id,customer_id:$customer_id, _token:token},
                success: function(data) {
                    // console.log(data.data);
                    // console.log(data.service_name);
                    var tab_id ="table"+$service_id;
                    // console.log(tab_id);
                    // var myEle = document.getElementById("myElement");
                    if(document.getElementById(tab_id) )
                    {
                        alert("service already exist");
                    }else
                    {
                        // console.log("not-exist");
                        //service_items
                        html = '<table class="table" id="table'+$service_id+'">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan="5"><center>' + data.service_name +'</center></th>	'+										
                                    '</tr>'+
                                    '<tr>'+
                                        '<th  style="width: 25%;">Item</th>'+
                                        '<th  style="width: 10%;">Qty</th>'+
                                        '<th  style="width: 15%;">Addons</th>'+
                                        '<th style="width: 20%;">Image</th>'+	
                                        '<th style="width: 25%;">Note</th>'+		
                                        '<th style="width: 5%;"></th>	'+										
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    '<tr>'+
                                        '<td>'+
                                            '<input type="hidden" value="'+$service_id+'" class="form-control" placeholder="Note" name="service_id['+$service_id+']"/>'+
                                            '<select class="form-control item_select" name="item_id['+$service_id+'][]" >'+
                                                data.data +
                                            '</select>'+
                                        '</td>'+

                                        '<td>'+
                                            '<input type="number" class="form-control calc_num" value="0" name="pickup_qty['+$service_id+'][]" id ="pickup_qty['+$service_id+'][]" onchange="calc_value('+$service_id+')"/>'+
                                        '</td>'+

                                        '<td class="item_addons">'+
                                            '--No addon--'+
                                        '</td>'+

                                        '<td>'+
                                            '<input type="file" class="form-control" placeholder="Image" name="item_image['+$service_id+'][]" id="item_image"/>'+
                                            '<label for="item_image" id="image"><i class="fas fa-pencil-alt"></i> select image</lable>'+
                                        '</td>'+
                                        '<td>'+
                                            '<input type="text" class="form-control" placeholder="Note" name="item_note['+$service_id+'][]"/>'+
                                        '</td>'+
                                        
                                        '<td>'+
                                            '<a href="javascript:void(0);" data-id="'+$service_id+'" class="btn btn-danger del_row btn-sm btn-clean btn-icon">'+
                                                '<i class="la la-minus"></i>'+
                                            '</a>'+
                                        '</td>'+
                                    '</tr>'+

                                '</tbody>'+
                            '</table>'+
                            '<table id="table_summary'+$service_id+'" class="table">'+
                                '<tbody>'+
                                    '<tr>'+
                                        '<th width="25">'+
                                            'Total Quantity'+
                                        '</th>'+
                                        '<td width="25%">'+
                                            '<input type="number" class="form-control" value="0" name="qty['+$service_id+']" id="qty['+$service_id+']" readonly="true" />'+
                                        '</td>'+
                                    
                                        '<th width="25">'+
                                            'Total Weight'+
                                        '</th>'+
                                        '<td width="25%">'+
                                            '<input type="number" class="form-control" value="0" name="weight['+$service_id+']" />'+
                                        '</td>'+
                                    '</tr>'+
                               '<tbody>'+
                            '</table>'+

                            '<table id="table_tab'+$service_id+'" class="table">'+
                                '<tbody>'+
                                    '<tr>'+
                                        '<td colspan="4" style="text-align:right"> Add New Row</td>'+
                                        '<td width="5%"> <input class="btn btn-success btn-sm add_row" data-id="'+$service_id+'" type="button"  value="+"></td>'+
                                    '</tr>'+
                               '<tbody>'+
                            '</table>';

                            $('#service_items').append(html);
                    }
                }
            });
        }

        // function fetch_addons($item_id){
            //     var service_id  = document.getElementById('service').value;
            //     console.log("Addon Serivce id: "  + service_id );
            //     console.log("Addon Item id: "  +  $item_id);

            //     var token = $("input[name='_token']").val();
            //     $.ajax({
            //         url: "{{ url('fetch_addons') }}",
            //         method: 'POST',
            //         data: {item_id:$item_id,service_id:service_id, _token:token},
            //         success: function(data) {
            //             console.log(data.data);
                        
            //         }
            //     });

        // }
        
        $(document).on('change','.item_select', function(){
            var currSelect = $(this);
            console.log(currSelect);
            var $item_id = $(this).val();
            var service_id  = document.getElementById('service').value;
            // console.log("Addon Serivce id: "  + service_id );
            // console.log("Addon Item id: "  +  $item_id);

            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_addons') }}",
                method: 'POST',
                data: {item_id:$item_id,service_id:service_id, _token:token},
                success: function(data) {
                    // console.log(data.data);
                    if(data.data){
                        currSelect.parent('td').parent('tr').find('.item_addons').html(data.data);
                    }else{
                        currSelect.parent('td').parent('tr').find('.item_addons').html('--No addon--');
                    }
                }
            });
        });

        $(document).on('click','.add_row', function(){
            var service_id = $(this).data('id');
            var table_id = 'table'+service_id;
                var $el = $('#'+table_id+' tbody tr:first-child').clone(); 
                // $el.find('input').val('');
                $el.find('.item_addons').html('--No addon--');
                // $el.find('option').val(0);
                // $('.selDiv option[value="SEL1"]')
                $el.find('.calc_num').val(0);
                $el.find('.item_select').val(0);
                $('#'+table_id+' tbody').append($el); 
                calc_value(service_id)
        })


        $(document).on('click','.del_row', function(){
            var service_id = $(this).data('id');
            var table_id = 'table'+service_id;
            var table_tab = 'table_tab'+service_id;
            var table_summary = 'table_summary'+service_id;
            
            // console.log(table_id);
            var rowCount = $('#'+table_id+' tbody tr').length;
            // console.log(rowCount);
            if(rowCount==1){
                $("#"+table_id).remove();
                $("#"+table_tab).remove();
                $("#"+table_summary).remove();
            }else{
                var tr = $(this).parent('td').parent('tr');
                tr.remove();
            }
            calc_value(service_id);
        })

        function calc_value(service_id){
            var table_summary   = 'table_summary'+service_id;
            var item_qty        = 0;
            var q               = 0;

            var qty     = $("input[name='pickup_qty\\["+service_id+"][\\]']")
                            .map(function(){
                                return $(this).val();
                            }).get();

            for (x in qty) {
                q = parseInt(qty[x]);
                item_qty += q;
            }
            $("input[name='qty["+service_id+"]']").val(item_qty);
            // $('#qty['+service_id+']').val(item_qty);
            // console.log(item_qty);
        }
    </script>
@endsection

