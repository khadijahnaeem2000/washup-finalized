@extends('layouts.master')
@section('title','Schedule Route Plan')
@section('content')
<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>

<script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
<script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script>


 <!-- @include( '../sweet_script') -->
    <style type="text/css">
        .data_center{
            vertical-align: middle;
            text-align: center;
        }
        #rider_table th{
            text-align: center;
            vertical-align: middle;
        }
        #order_table th, #order_table td{
            text-align: center;
            vertical-align: middle;
        }
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
        .tRed{
            color:red;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }
        .tGreen{
            color:green;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }

        .tBlue{
            color:blue;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }
	</style>
    <!-- BEGIN::Summary -->
    <div class="row">
        
        <div class="col-lg-12">
        <div id= "loaderDiv"><i class="fas fa-spinner fa-spin" style="position:absolute; left:50%; top:50%;font-size:80px; color:#3a7ae0"></i> </div>
            <!--begin::Card-->
            {!! Form::open(array('id'=>'summary_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Summary</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->

                            <span style="margin-right: 15px; font-weight:bold">Distribution Hub</span>  
                            <div class="input-icon" style="margin-right: 5px">
                                {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id')) !!}
                            </div>

                            <span style="margin-right: 15px; font-weight:bold">Plan for</span>  
                            <div class="input-icon" style="margin-right: 5px">
                                <input type="date" name = "plan_date" id="plan_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                            </div>
                        
                            <!-- Button trigger Pickup Order modal-->
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="plan_btn"href="javascript:void(0)" id=""> Search</a>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="summary_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div><br>
    <!-- END::Summary -->

    <!-- BEGIN:: Rider Profile -->
    <div class="row">
        
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'riders_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Riders' Profile</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <input type="button" class="btn btn-primary btn-sm font-weight-bolder" id ="btn_update_riders"  value="Update Riders"  disabled>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="riders_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div><br>
    <!-- END:: Rider Profile -->

    <!-- BEGIN:: Orders -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'orders_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Manage Route Plan</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->

                            <!-- <span style="margin-right: 15px; font-weight:bold">Plan for</span>   -->
                            <div class="input-icon" style="margin-right: 5px">
                                <input type="hidden" name = "pln_date" id="pln_date" value=""  class="form-control btn-sm" />
                            </div>
                            
                            <input type="button" class="btn btn-success btn-sm font-weight-bolder" id ="btn_resort"  value="Save Sorting"  disabled>

                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->

                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="orders_table">
                            <h4 style='text-align:center; padding:10px'> Please press search button  </h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <input type="button"  class="btn btn-primary mr-2" value="Get Route" onclick="fn_set()" id="get_route" disabled/>
                                <input type="button" class="btn btn-success font-weight-bolder" id ="btn_store_resort"  value="Save and Resort" disabled>

                                <!-- <input type="button"  class="btn btn-primary mr-2" value="Get Route & Save" onclick="fn_get_route()" id="get_route"/>
                                <input type="button"  class="btn btn-primary mr-2" value="show Route" onclick="fn_show_route()"  id="show_route" /> -->
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <!-- END:: Orders -->

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=places"></script>

    <!-- BEGIN::AJAX CRUD Order -->
    <script type="text/javascript">
        $(document).ready(function () { 
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                })
        });
        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <!-- END::AJAX CRUD Order -->

    
    <!--BEGIN:: re-draw-order table -->
    <script type="text/javascript">
        function empty_variable(){
            var re_route      = 0; // 0 means route NOT-stored and 1 means route stored
            last_rider       = '';
            last_route       = '';
            rt              = true;
            rds             = '';
            ch              = 0;
            dt              = '';
            sr              = '';
            rec             = [];
            tot_dist        = 0; 
            tot_time        = 0;
            all_dist        = [];
            increment       = 0 ;
            new_sr           = '';
          
            start_sr        = '';
            id, de, pre, cur, exp_dist=0, pre_index = 0, cur_index = 0, inc=0, inx = 0;

            route_created   = [];
        }

        $('#plan_btn').click(function (e) {
            empty_variable();
            $("#loaderDiv").show();
            e.preventDefault();
            // console.log("clicked");
            re_draw_all();
            // $("#get_route").attr("disabled", false);
            $("#btn_resort").attr("disabled", false);
            $("#btn_update_riders").attr("disabled", false);
            
            $("#loaderDiv").hide();
        });

        $('#btn_store_resort').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('route_plans.index') }}" +'/store_resort/';
            var form_id = '#orders_form';
            $("#loaderDiv").show();
            $.ajax({
                data: $(form_id).serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        re_route = 1;
                        re_draw_all();
                        $("#loaderDiv").hide();
                        toastr.success(data.success);
                    }else{
                        var txt = '';
                        var count = 0 ;
                        $.each(data.error, function() {
                            txt +=data.error[count++];
                            txt +='<br>';
                        });
                        toastr.error(txt);
                        $("#loaderDiv").hide();
                    }
                },
                error: function (data) {
                    $("#loaderDiv").hide();
                    toastr.error("Something went wrong!!!");
                    console.log('Error:', data);
                }
            });
        });

        $('#btn_resort').click(function (e) {
            e.preventDefault();
            $("#loaderDiv").show();
            var cus_url = "{{ route('route_plans.index') }}" +'/resort/';
            var form_id = '#orders_form';
            $.ajax({
                data: $(form_id).serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $("#loaderDiv").hide();
                    if(data.success){
                        $("#get_route").attr("disabled", false);
                        re_draw_all();
                      
                        toastr.success(data.success);
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
                    
                },

                
            });
        });
        
        $('#btn_update_riders').click(function (e) {
            e.preventDefault();
            var cus_url = "{{ route('route_plans.index') }}" +'/update_riders/';
            var form_id = '#riders_form';
            $("#loaderDiv").show();
            $.ajax({
                data: $('#riders_form').serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        re_draw_all();
                        $("#loaderDiv").hide();
                        toastr.success(data.success);
                        // order_table.draw();
                    }else{
                        var txt = '';
                        var count = 0 ;
                        $.each(data.error, function() {
                            txt +=data.error[count++];
                            txt +='<br>';
                        });
                        toastr.error(txt);
                    }
                    // table.draw();
                },
                error: function (data) {
                    $("#loaderDiv").hide();
                    toastr.error("Something went wrong!!!");
                    console.log('Error:', data);
                }
            });
        });

        function re_draw_all(){
         
            var tbl         = '';
            var fn          = '';
            var hub_id      = document.getElementById('hub_id').value; 
            var p_date      = document.getElementById('plan_date').value; 

            $('#pln_date').val(p_date);
            $("#summary_table").html("");
            $("#summary_table").append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
        
            tbl             = 'orders_table';
            fn              = 'fetch_route_orders';
            fn_redraw_table(hub_id,p_date,tbl,fn);
           
            
            tbl             = 'riders_table';
            fn              = 'fetch_route_riders';
            fn_redraw_table(hub_id,p_date,tbl,fn);
            
            setTimeout(() => {     
                tbl             = 'summary_table';
                fn              = 'fetch_route_summary';
                fn_redraw_table(hub_id,p_date,tbl,fn);
            }, 2000);
            
           
        }

        function fn_redraw_table(hub_id,p_date,tbl,fn){
            $("#"+tbl).html("");
            var token  = $("input[name='_token']").val();
            $.ajax({
                url: fn,
                method: 'POST',
                data: {hub_id:hub_id,p_date:p_date, _token:token},
                // dataType: 'json',
                beforeSend:function () {
                    $("#"+tbl).html("");
                    $("#"+tbl).append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                },

                success: function (data) {
                    if(data.details){
                        // console.log(data.details);
                        $("#"+tbl).html("");
                        $("#"+tbl).html(data.details);
                    }else{

                        $("#"+tbl).html("");
                        $("#"+tbl).append("<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2>");
                        if((data.error) && (tbl == "summary_table") && (re_route == 0)){
                            toastr.error(data.error);
                        }
                        // if(tbl == 'summary_table'){
                        //     toastr.error(data.error);   
                        // }
                    }
                },
                error: function (data) {
                    toastr.error("Something went wrong!!!");
                    console.log('Error:', data);
                }
            });
           
        }
    </script>
    <!--END:: re-draw-order table -->

    <!-- BEGIN:: Defining Vairables -->
    <script type="text/javascript">
        
        var new_sr           = '';
        var last_rider       = '';
        var last_route      = '';
        var rt              = true;
        var rds             = '';
        var ch              = 0;
        var dt              = '';
        var sr              = '';
        var rec             = [];
        var tot_dist        = 0; 
        var tot_time        = 0;
        var all_dist        = [];
        var increment       = 0 ;
        var start_sr        = '';
        var id, de, pre, cur, exp_dist=0, pre_index = 0, cur_index = 0, inc=0, inx = 0;

        var route_created   = [];

        //setting up Google Map 
        var source, destination;
        var directionsDisplay;
        var directionsService = new google.maps.DirectionsService();
        google.maps.event.addDomListener(window, 'load', function () {
            directionsDisplay = new google.maps.DirectionsRenderer({ 'draggable': true });
        });
     
        
    </script>
    <!-- END:: Defining Vairables -->

    <!-- BEGIN:: Route plan function -->
    <script type="text/javascript">
        function fn_set_array(){
            // console.log("start");
            // console.log(JSON.stringify(rds));
            if((Object.keys(rds).length)  == 0 ) return "exit";
            // console.log("Length: " + rec.length);
            if(rec.length > 0 ){
                // console.log("hhhh");
                return false;
            }
            
            $.each(rds, function(rds_key, rds_value){
                // console.log("++++++++++++++");
                // console.log(JSON.stringify(rds_value));
                // console.log((Object.keys(rds).length));
                // console.log("++++++++++++++");
                if((JSON.stringify(rds_value) == '{}') || ((Object.keys(rds_value).length) == 0)){
                    // console.log('rds_value == {}');
                    // console.log('source: ' +sr);
                    delete rds[rds_key];
                    sr    = start_sr;
                    return;
                }

                
                // rds_value: riders value
                // rt_value: route value
                $.each(rds_value, function(rt_key, rt_value){
                    // console.log("++++++++++++++");
                    // console.log(JSON.stringify(rt_value));
                    // console.log("++++++++++++++");
                    if((JSON.stringify(rt_value) == '{}') || ((Object.keys(rt_value).length) == 0)){
                        delete rds[rds_key][rt_key];
                        return;
                    }

                    $.each(rt_value, function(ts_key, ts_value){
                        // console.log("************timeslot***********");
                        
                        // console.log("ts_value.length: " + (Object.keys(ts_value).length));
                        // console.log("rt_value.length: " + (Object.keys(rt_value).length));
                        // console.log(JSON.stringify(ts_value));
                        
                        // console.log("***************************");

                        if((JSON.stringify(ts_value) == '{}') || ((Object.keys(ts_value).length) == 0)){
                         
                            delete rds[rds_key][rt_key][ts_key];

                            if((JSON.stringify(rt_value) == '{}') || ((Object.keys(rt_value).length) == 0)){
                                // console.log("rt_value.length: " + (Object.keys(rt_value).length));
                                delete rds[rds_key][rt_key];
                                if((JSON.stringify(rds_value) == '{}') || ((Object.keys(rds_value).length) == 0)){
                                    // console.log("rds_value.length: " + (Object.keys(rds_value).length));
                                    delete rds[rds_key];
                                }
                                return ;
                            }
                            // console.log("::::::::::::::::::::::::::")
                            return false;
                        }
                      
                        $.each(ts_value, function(key, value){

                            if ((typeof value !== 'undefined')) {
                                var chk = true;
                                $.each(rec, function(k, v){
                                    if (typeof value.id !== 'undefined') {
                                        if((v.id) == (value.id)){
                                            chk = false;
                                        }
                                    }
                                })
                                if(chk){
                                    rec.push(value);
                                    delete rds[rds_key][rt_key][ts_key][key];
                                    // return;
                                    
                                }
                            }else{
                                // console.log(JSON.stringify(value));
                                delete rds[rds_key][rt_key][ts_key][key];
                                return false;
                            }
                       
                            
                        })

                        delete rds[rds_key][rt_key][ts_key];
                       
                   
                          
                        
                        return false;
                    })
                 
                    return false;
                })
             
                return false;
            })
            return false;
        }

        function fn_set(){
            var cus_url = "{{ route('route_plans.index') }}" +'/get_route/';
            var form_id = '#summary_form';
            $.ajax({
                data: $(form_id).serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.data){
                        $("#get_route").attr("disabled", true);
                        rds     = data.rds; 
                        // console.log(JSON.stringify(rds));
                        $.each(rds, function(rds_key, rds_value){
                            $.each(rds_value, function(rt_key, rt_value){
                                $.each(rt_value, function(ts_key, ts_value){
                                    // console.log("Rider id: "+ rds[rds_key][rt_key][0].rider_id);
                                    // console.log("////////////////////////////");
                                    // console.log(JSON.stringify(rt_value));
                                    // console.log("*********************");
                                    
                                    last_route  = rds[rds_key][rt_key][ts_key][0].route;
                                    last_rider  = rds[rds_key][rt_key][ts_key][0].rider_id;
                                    sr          = rds[rds_key][rt_key][ts_key][0].sr;
                                    start_sr    = sr;
                                    // console.log(sr);
                                    fn_get_route();
                                    $("#loaderDiv").show();
                                    return false;
                                })
                                return false;
                            })
                            return false;
                        })
                    }else{
                        var txt     = '';
                        var count   = 0 ;
                        $.each(data.error, function() {
                            txt += data.error[count++];
                            txt += '<br>';
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
        }

        function fn_get_route(){
            // console.log("get_route");
            // $("#get_route").attr("disabled", true);
            rt      = fn_set_array();
            // console.log(rt);
            // console.log("-----------in fn-------")
            // console.log(JSON.stringify(rec));
            // console.log("-----------in fn-------")
            // console.log("rt: "  + rt );
            if(rt == "exit") return;
            if((JSON.stringify(rec) )== '[]'){
                // console.log("===rec has no data===");
                fn_get_route();
            }
            // console.log("rec.length" + rec.length);

            if(rec.length == 0){
                $("#btn_store_resort").attr("disabled", false);
                $("#btn_resort").attr("disabled", true);
                $("#btn_update_riders").attr("disabled", true);
                $("#loaderDiv").hide();
            }
         
            if(rt == false){
                // console.log("false");
                all_dist  = []; 
                for(var a=0; a<rec.length; a++){
                    // setTimeout(function() {
                        // console.log("a: " + a);
                        fn_set_cordinate(a);
                    // },300 * a); 
                }
                
            }
            // console.log("calling show route");
            //     fn_show_route();
            return ;
           
        }

        function fn_set_cordinate(a){
            // console.log("set cordinate");
            setTimeout(function() { 
                // console.log(rec);
                // console.log("a: " + rec[a].id);
                // console.log("rec.length - after:" +rec.length);

                id          = (rec[a].id);
                de          = (rec[a].des);

                // console.log("*******************************")
                // console.log("last_route: " +last_route)
                // console.log("new_route: " + rec[a].route)
                // console.log("*******************************")

                  // console.log("*******************************")
                // console.log("last_rider: " +last_rider)
                // console.log("new_rider: " + rec[a].rider_id)
                // console.log("*******************************")

                if(last_route  != rec[a].route){
                    // if( (last_route  != rec[a].route) || (last_rider  != rec[a].rider_id) ){
                    // console.log("DANGER");
                    last_route  = rec[a].route;
                    // console.log("add: " + rec[a].hub_latitude +", " +rec[a].hub_longitude);
                    // console.log("objs[0]['order_id']: " + rec[j]['hub_latitude'] +", " +rec[j]['hub_longitude']);
                    // console.log("last_route: " + last_route);
                    // if route get changed, then distribution hub = sr, will be assigned to new_sr so that for next order source address will be this distribution hub address
                    sr  = rec[a].hub_latitude +", " +rec[a].hub_longitude;
                    //  sr = new_sr;
                }
                // }else{
                //      new_sr  = rec[a].latitude +", " +rec[a].longitude; 
                //      sr = new_sr;
                // }
                rt          = fn_calc_distance(sr,de,id);
                // console.log("sr: " + sr);
                if(rt == true){
                   if(a==(rec.length-1)){
                    //    console.log
                        // console.log(rec);
                        setTimeout(function() { 
                            // if(typeof (rec[a].id) !== 'undefined' ){
                                fn_show_route();
                                // $("#loaderDiv").hide();
                                fn_get_route();
                            // }
                        },3000 ); 

                    }
                }
                return true;
            },2000 * a); 
            
        }
      
        // Separating distance from its unit
        function fn_explode(data){
            var exp_data = data.split(" ");
            return exp_data[0];
        }

        // Storing distance object in array
        function fn_store_distance(distance,time,id){
            exp_dist = fn_explode(distance);
            var obj = {
                id: Number(id),
                dist: Number(exp_dist),
                time: time
            }
            all_dist.push(obj);
            // console.log("Id: " + id);
            // console.log("Time:" + time);
            // console.log("Distance: " + distance);
            // console.log("==========================================");
            return true;
        }

        // Sorting stored object by a "dist" key
        function fn_get_sort_order(prop) {    
            return function(a, b) {    
                if (a[prop] > b[prop]) {    
                    return 1;    
                } else if (a[prop] < b[prop]) {    
                    return -1;    
                }   
                return 0;    
            }    
        }  

        // Showing sorted object
        function fn_show_route(){
            // $("#get_route").attr("disabled", false);
            // console.log("show route");
            // console.log(JSON.stringify(rec));
            all_dist.sort(fn_get_sort_order("dist"));
            put_seq(all_dist);
            return;
          
        }

        function put_seq(objs){
            // console.log(objs.length);
            if(objs.length == 0) return false;
            var index       = objs[0]['id'];
            // console.log("index: " + index);
            // console.log("objs[0]['time']: " + objs[0]['time']);
            tot_dist        += objs[0]['dist'];
            tot_time        += parseInt(fn_explode(objs[0]['time']));
           

            $("input[name='dist\\["+index+"]']").val(objs[0]['dist']);
            $("input[name='time\\["+index+"]']").val(objs[0]['time']);
   
            if(objs.length>-1){
                for(var j=0; j<rec.length; j++){
                    if(objs[0]['id']==rec[j]['id']){
                        // console.log("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!")
                        // console.log("route_id" + rec[j]['route']);
                        // console.log("Last Rider id: " + last_rider);
                        // console.log("Rider id: " + rec[j]['rider_id']);
                        // console.log("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!")
                        var new_sr      = rec[j]['latitude'] +", " +rec[j]['longitude'];
                        
                        // console.log("---------------------------")
                        // console.log("add2: "+ rec[j]['latitude'] +", " +rec[j]['longitude'])
                        // console.log("new_route: " + rec[j]['route'])
                        // console.log("---------------------------")

                        // var new_route   = rec[j]['route'];
                        // if(last_route  != rec[j]['route']){
                        //     last_route  = rec[j]['route'];
                        //     // console.log("objs[0]['order_id']: " + rec[j]['hub_latitude'] +", " +rec[j]['hub_longitude']);
                        //     // console.log("last_route: " + last_route);
                        //     // if route get changed, then distribution hub = sr, will be assigned to new_sr so that for next order source address will be this distribution hub address
                        //     var new_sr  = rec[j]['hub_latitude'] +", " +rec[j]['hub_longitude'];
                        // }else{
                        //     // if route get not changed, then address of this order will be assigned to sr, so that for next order source will be this order's address

                        // }
                            // var new_sr  = rec[j]['latitude'] +", " +rec[j]['longitude'];
                        //     console.log("-----------------------------");
                        // console.log("inc: " + inc)
                        if(last_rider  != rec[j]['rider_id']){
                            inc = 1;
                            last_rider = rec[j]['rider_id'];
                        }else if(sr != new_sr){
                            inc++;
                        }
                        $("input[name='seq\\["+index+"]']").val(inc);
                        
                        // console.log("***********************");
                        // console.log("inc: " + inc)
                        sr = new_sr;
                        // console.log("sr: " + sr)
                        // console.log("---------------------------")
                        $("input[name='address\\["+index+"]']").val(sr);
                        var removeIndex = rec.map(function(item) { return item.id; }).indexOf(rec[j]['id']);
                        // console.log("INDEX: " + removeIndex);
                        // remove object
                        rec.splice(removeIndex, 1);
                        break;
                    }
                }
                
            }
            return true;
        }
        
        // calculating distance, time using the google map; s: source; d: destination
        function fn_calc_distance(s,d,id) {
            // console.log("calc distance");
            // var khi = new google.maps.LatLng(24.876, 67.065);
            // var mapOptions = {
            //     zoom: 7,
            //     center: khi
            // };
            // map = new google.maps.Map(document.getElementById('dvMap'), mapOptions);
            // directionsDisplay.setMap(map);
            // directionsDisplay.setPanel(document.getElementById('dvPanel'));
        
            //*********DIRECTIONS AND ROUTE**********************//
            source      = s;
            destination = d;
        
            var request = {
                origin: source,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        
            //*********DISTANCE AND DURATION**********************//
            var service = new google.maps.DistanceMatrixService();
            service.getDistanceMatrix({
                origins: [source],
                destinations: [destination],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                avoidHighways: false,
                avoidTolls: false
            }, function (response, status) {
                // console.log();
                if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
                    var distance = response.rows[0].elements[0].distance.text;
                    var duration = response.rows[0].elements[0].duration.text;
                    // var dvDistance = document.getElementById("dvDistance");
                    // dvDistance.innerHTML = "";
                    // dvDistance.innerHTML += "Distance: " + distance + "<br />";
                    // dvDistance.innerHTML += "Duration: " + duration;
                    // console.log("source: " + response['originAddresses']);
                    // console.log("destin: " + response['destinationAddresses']);
                    // console.log("Distance: " + distance + "Duration: " + duration);
                    // console.log("id: " + id);
                   fn_store_distance(distance,duration,id);
                   return true;
                } else {
                    alert("Unable to find the distance via road.");
                    return false;
                }
            });

            return true;
        }
       
    </script>
    <!-- END:: Route plan function -->
  
   

@endsection
