@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_script')
    <style type="text/css">
        #loaderDiv{
            width:100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.2);
            z-index:9999;
            display:none;
        }
    </style>
    <div class="row">
        <?php $tot_weight = 0?>
        <div class="col-lg-12">
            <!--begin::Card-->
            <div id= "loaderDiv"><i class="fas fa-spinner fa-spin" style="position:absolute; left:50%; top:50%;font-size:80px; color:#3a7ae0"></i> </div>
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Inspect Order (Content) > 
                   
                        Wash house: {{$data->washhouse_name}} 
                    </h3>
                    <div class="card-toolbar">
                        
                        <a  href="{{ route('order_inspects.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <audio id="alarm" src="{{ asset('/music/alarm.wav') }}" preload="auto"></audio>
                <!--begin::Form-->
                
                {!! Form::open(array('id'=>'order_form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    
                    {{  Form::hidden('id', $data->id,array('id' => 'id') ) }}
              
                    <div class="card-body">

                        <!-- name, contact no, order and status id -->
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name ')) !!}
                                {{ Form::text('name', $data->name, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', $data->customer_id, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: ')) !!}
                                    {{ Form::number('contact_no', $data->contact_no, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'readonly' => 'true')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                           
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('order_id','Order No ')) !!}
                                {{ Form::text('order_id', $data->order_id, array('placeholder' => 'Enter order no','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('order_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('status_id','Status')) !!}
                                    {!! Form::select('status_id', $statuses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('status_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('status_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- rating, polybags and delivery date -->
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('iron_rating','Iron Rating <span class="text-danger">*</span> ')) !!}
                                    {!! Form::select('iron_rating', [0=>'-- Select --']+$ratings,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('iron_rating'))  
                                        {!! "<span class='span_danger'>". $errors->first('iron_rating')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('softner_rating','Softner Rating <span class="text-danger">*</span>')) !!}
                                    {!! Form::select('softner_rating', [0=>'-- Select --']+$ratings,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('softner_rating'))  
                                        {!! "<span class='span_danger'>". $errors->first('softner_rating')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('polybags_qty','Polybags Qty<span class="text-danger">*</span>')) !!}
                                {{ Form::number('polybags_qty', null, array('placeholder' => 'Enter Polybags Qty','class' => 'form-control','min'=>'1','autofocus'=>'true','required'=>'true')) }}
                                    @if ($errors->has('polybags_qty'))  
                                        {!! "<span class='span_danger'>". $errors->first('polybags_qty')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                    {{ Form::date('delivery_date', $data->delivery_date, array('placeholder' => 'Enter delivery date','class' => 'form-control','readonly' => 'true')) }}
                                    @if ($errors->has('delivery_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('delivery_date')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Permanent and order Note -->
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('permanent_note','Permanent Note: ')) !!}
                                    {!! Form::textarea('permanent_note', $data->permanent_note, array('placeholder' => 'Permanent Note','rows'=>5, 'class' => 'form-control','readonly' => 'true' )) !!}
                                    @if ($errors->has('permanent_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('permanent_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('order_note','Order Note: ')) !!}
                                    {!! Form::textarea('order_note', $data->order_note, array('placeholder' => 'Order Note','rows'=>5, 'class' => 'form-control','readonly' => 'true')) !!}
                                    @if ($errors->has('order_note'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_note')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal Form for scanning  -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="card-toolbar" style="float:right">
                                    <!-- {{ route('order_inspects.index') }} -->
                                    <a  href="" class="btn btn-primary btn-sm " data-toggle="modal" data-target="#scan_tags">
                                        <i class="fas fa-barcode"></i> Scan Tags
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Service items-->
                        <div id="service_items">
                            <?php foreach($selected_services as $service_key => $service_value){?>
                                <table class="table" id="table<?php echo $service_value->service_id?>">
                                    <thead>
                                        <tr>
                                            <th colspan="7"><center><?php echo $service_value->service_name?> </center></th>							
                                        </tr>
                                        <tr>
                                            <th  style="width: 30%;">Item</th>
                                            <th  style="width: 10%;">Qty</th>
                                            <th  style="width: 10%;">Scan Qty</th>
                                            <th  style="width: 10%;">B.T Qty</th>
                                            <th  style="width: 10%;">N.R Qty</th>
                                            <th style="width: 10%;">HFQ Qty</th>	
                                            <th style="width: 20%;">Reason</th>		
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($selected_items as $item_key => $item_value){
                                            if( $service_value->service_id ==  $item_value->service_id){?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" value="<?php echo $service_value->service_id?>" class="form-control" name="service_id[<?php echo $service_value->service_id?>]"/>
                                                    <input type="hidden" class="form-control" readonly  value = "{{ $item_value->item_id }}" name="item_id[<?php echo $service_value->service_id?>][]" >
                                                    <input type="text" class="form-control" readonly  value = "{{ $item_value->item_name }}" name="item_name[<?php echo $service_value->service_id?>][]" >
                                                    <input type="hidden" class="form-control" readonly  value = "{{ $item_value->ord_itm_id }}" name="ord_itm_id[<?php echo $service_value->service_id?>][]" >
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"  readonly value="<?php echo $item_value->pickup_qty?>" name="pickup_qty[<?php echo $service_value->service_id?>][]" />
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" readonly min="0"  value="<?php if (isset($item_value->scan_qty)) echo $item_value->scan_qty; else echo 0;?>" name="scan_qty[<?php echo $service_value->service_id?>][]" />
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"  min="0"  value="<?php if (isset($item_value->bt_qty)) echo $item_value->bt_qty; else echo 0;?>" name="bt_qty[<?php echo $service_value->service_id?>][]" />
                                                </td>
                                               
                                                <td >
                                                    <input type="number" class="form-control"  min="0"  value = "<?php if (isset($item_value->nr_qty)) echo $item_value->nr_qty; else echo 0;?>" name="nr_qty[<?php echo $service_value->service_id?>][]">
                                                </td>

                                                <td >
                                                    <input type="number" class="form-control calc_num" min="0" value = "<?php if (isset($item_value->hfq_qty)) echo $item_value->hfq_qty; else echo 0;?>" name="hfq_qty[<?php echo $service_value->service_id?>][]" >
                                                </td>
                                                <td >
                                                    <input type="text" class="form-control" value = "<?php echo $item_value->reason; ?>" name="reason[<?php echo $service_value->service_id?>][]"  placeholder="Enter reason">
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
                                            <?php $tot_weight+=($service_value->service_weight);?>
                                                <input type="number" step = "any" class="form-control" value="<?php echo $service_value->service_weight?>" name="weight[<?php echo $service_value->service_id?>]" />
                                            </td>
                                        </tr>
                                    <tbody>
                                </table>

                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                       
                            <div class="col-lg-2 text-left">
                                <b>{!! Html::decode(Form::label('packed_weight','Packed Weight: ')) !!}</b>
                            </div>
                            <div class="col-lg-2 text-left">
                                {{ Form::number('packed_weight', null, array('placeholder' => 'Enter Packed Weight','class' => 'form-control','min'=>'1','step'=>'any','required'=>'true')) }}
                            </div>

                            <div class="col-lg-2 text-left">
                                <b>{!! Html::decode(Form::label('tot_weight','Total weight: ')) !!}</b>
                            </div>

                            <div class="col-lg-2 text-left">
                                <b>{{ Form::number('tot_weight', $tot_weight, array('class' => 'form-control','required'=>'true','readonly'=>'true')) }} </b>
                            </div>
                            <div class="col-lg-4 text-right">
                                <!-- <button type="submit" class="btn btn-primary mr-2">Save</button> -->
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm font-weight-bolder" id="finalize_orders_btn">
                                    <i class="flaticon2-check-mark"></i>Save
                                </a>
                                <!-- <button type="reset" class="btn btn-secondary">Reset</button> -->
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>

    <!-- BEGIN :: Scan Modal Form -->
    <div class="modal" tabindex="-1" role="dialog" id="scan_tags">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Scan Tags</h4>
                    <button id="pause_sound" class="btn btn-danger btn-sm"><i class="icon-md fas fa-volume-mute"></i></button>
                </div>
                
                {!! Form::open(array('id'=>'tagform','name'=>'tagform')) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!!Form::label('tag_code', '* Scan Tags')!!}
                                    {!! Form::text('tag_code', null, array('class' => 'form-control')) !!}
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a type="button" id="btn_scanned_tag" class="btn btn-primary">Scanning complete</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END :: Scan Modal Form -->
    <script>
        $(document).ready(function(){
            var tags_adn        = <?php echo json_encode($tags_adn); ?>;
            var order_tag       = <?php echo json_encode($order_tags); ?>;
            // console.log(tags_adn);
            var inc             = 0;
            var check           = 0;
            var tag_code        = '';
            var scan_order_tag  = [];
            
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
           
            function alert_msg(msg){
                document.getElementById('alarm').play();
                swalWithBootstrapButtons.fire({
                    title: 'Error',
                    text: msg,
                    icon: 'error',
                    // showResetButton: true,
                    confirmButtonText: 'Ok!',
                    // cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        document.getElementById('alarm').pause();
                    }
                });
            }

            function has_tag_addons(code){
                var adn = [];
                for(var i = 0; i < tags_adn.length; i++) {
                    if(parseInt(tags_adn[i].id) == code){
                        adn.push(tags_adn[i].name);
                    }
                }
                return adn;
            }

            function update_tags_status_HFQ(code){
                console.log("code: " + code);
                var token = $("input[name='_token']").val();
                $.ajax({
                        url: "{{ url('update_tags_status_HFQ') }}",
                        method: 'POST',
                        data: {code:code, _token:token},
                        success: function(data) {
                            if(data.data){
                            //    console.log("data.data: " + data.data);
                                toastr.success("Scanned successfully and tag has been put into HFQ qty");
                            }else{
                                // console.log("data.error: " + data.error);
                                toastr.error("Scanned successfully but tag was not put into HFQ qty");
                            }
                        }
                    });
            }

            function is_tag_valid(tCode){
                n               = tCode.length;
                n               = n-1;
                // console.log("n:" + n);
                // console.log("tcode:" + tCode);
                
                tCode           = tCode.slice(0, n);
                // console.log("tcode:" + tCode);
                loop1:
                for(var i = 0; i<order_tag.length; i++){
                    if(tCode == order_tag[i]){
                        // console.log("scan_order_tag.length" +scan_order_tag.length);
                        loop2:
                        for(var j = 0; j<scan_order_tag.length; j++){
                            // console.log("scan_order_tag[j]: " + scan_order_tag[j]);
                            if(tCode == scan_order_tag[j]){
                                check = 2;
                                break loop1; 
                            }
                        }
                        check  = 1;
                        break;
                    }else{
                        check = 0; ;
                    }
                }

                $('#tag_code').val('');
                if(check == 1){
                    var tg_adn = has_tag_addons(tCode);
                    if (tg_adn === undefined || tg_adn.length == 0) {
                        toastr.success("Scanned successfully");
                    }else{
                        var adns = tg_adn.toString();
                        adns = "Have all these addons: " + adns + " used????";
                        swalWithBootstrapButtons.fire({
                            title: 'Warning',
                            text: adns,
                            icon: 'warning',
                            showResetButton: true,
                            confirmButtonText: 'Ok!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                toastr.success("Scanned successfully");
                            }else{
                                update_tags_status_HFQ(tCode);
                            }
                        });
                    }
                    scan_order_tag[inc] = order_tag[i]; 
                    inc++;
                    return 1;
                }else if(check == 2){
                    // toastr.error("Tag already scanned")
                    alert_msg("Tag already scanned");
                }else{
                    // toastr.error("Item not belongs to this Order");
                    alert_msg("Item not belongs to this Order");
                }
                return 0;
            }

            $('#btn_scanned_tag').on('click', function () {
                
                // e.preventDefault();
                // console.log("scan_order_tag: " + scan_order_tag);
                if(scan_order_tag.length>0){
                    var token = $("input[name='_token']").val();
                    $.ajax({
                        url: "{{ url('update_order_tags_status') }}",
                        method: 'POST',
                        data: {scan_order_tag:scan_order_tag, _token:token},
                        success: function(data) {
                            if(data.data){
                            //    console.log("data.data: " + data.data);
                            toastr.success("Scanned tag saved successfully");
                            $('#scan_tags').modal('hide');
                            location.reload(true);
                                
                            }else{
                                toastr.error("Scanned tag not saved successfully");
                                $('#scan_tags').modal('hide');
                            }
                        }
                    });
                }else{
                    toastr.error("No tag was scanned");
                }
            });  

            $('#scan_tags').on('shown.bs.modal', function () {
                $('#tag_code').focus();
            }); 

            $('#pause_sound').click(function (e) {
                document.getElementById('alarm').pause();
            });
          

            $( '#tag_code' ).on( 'keypress', function( e ) {
                if( e.keyCode === 13 ) {
                    e.preventDefault();
                    tag_code = $('#tag_code').val();
                    var myAudio  = document.getElementById('alarm');
                    if (myAudio.duration > 0 && !myAudio.paused) {
                        $('#tag_code').val('');
                        toastr.error("Last Scanned tag was not belong to this order.");
                    }else{
                        if(is_tag_valid(tag_code)){
                        
                        // console.log("scan_order_tag: " +  scan_order_tag);
                    }
                    }
                    
                }
            });
        });
    </script>

    <!-- BEGIN :: Show Customer Detail -->
    <script type="text/javascript">
       $(document).ready(function(){
      
        var contact_no = document.getElementById('contact_no').value;  
            show_details(contact_no);
        });
        
        function show_details($contact_no){
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetchCustomerDetail') }}",
                method: 'POST',
                data: {contact_no:$contact_no, _token:token},
                success: function(data) {
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
                }
            });
        }
    </script>
    <!-- END :: Show Customer Detail -->
    <script type="text/javascript">
        $(function () {
                // Ajax request setup
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
        });

        function cus_validate(){
            

           
            if((($('#iron_rating').val()) <1)){
                $('#iron_rating').focus();
                return "Please select iron rating ";
            }

            if((($('#softner_rating').val()) <1)){
                $('#softner_rating').focus();
                return "Please select softner rating ";
            }

            if((($('#polybags_qty').val()) == '') ) {
                $('#polybags_qty').focus();
                return "Please enter polybags qty";
            }

            if((($('#polybags_qty').val()) <1)){
                $('#polybags_qty').focus();
                return "Please enter polybags qty > 1 ";
            }


            if((($('#packed_weight').val()) == '') ) {
                $('#packed_weight').focus();
                return "Please enter packed weight";
            }
            
            if((($('#packed_weight').val()) <1)){
                $('#packed_weight').focus();
                return "Please enter packed weight > 1";
            }

            return true;
        }
        $('#finalize_orders_btn').click(function (e) {
                e.preventDefault();
                var msg =    cus_validate();

                if( msg != true){
                    toastr.error(msg);
                    return;
                }
                
                $("#loaderDiv").show();
                
                var order_id = $('#id').val();
                // console.log( $('#order_form').serialize());
                $.ajax({
                    data: $('#order_form').serialize(),
                    url: "{{ url('inspect_order') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $("#loaderDiv").hide();
                        if(data.success){
                            // fn_redraw_table(dt,tbl,fn);
                            // re_draw_all();
                            toastr.success(data.success);
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "Do you want to print polybags!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, print polybags!'
                            }).then((result) => {
                                console.log(result);
                                if (result.isConfirmed) {
                                    window.location.href = "/order_inspects/show_bags/"+order_id;
                                }else if (result.isDismissed) {
                                    window.location.href = "{{ route('order_inspects.index') }}";
                                    // Swal.fire('Changes are not saved', '', 'info')
                                }
                            })

                        }else{
                            var txt = '';
                            var count = 0 ;
                            $.each(data.error, function() {
                                txt +=data.error[count++];
                                txt +='<br>';
                            });
                            toastr.error(txt);
                        }
                    },
                    error: function (data) {
                        $("#loaderDiv").hide();
                        toastr.error("Something went wrong!!!");
                        console.log('Error:', data);
                    }
                });
                
                

        });
    </script>
@endsection

