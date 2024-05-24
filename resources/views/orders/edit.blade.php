@extends('layouts.master')
@section('title','Order')
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
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['orders.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'autofocus' => '' ,'onkeyup'=>'show_details(this.value)')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('status_id','Status')) !!}
                                    {!! Form::select('status_id', $statuses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('status_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('status_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('pickup_timeslot_id','Pickup Time Slot: ')) !!}
                                    {!! Form::select('pickup_timeslot_id', $time_slots,null, array('class'=> 'form-control')) !!}
                                    @if ($errors->has('pickup_timeslot_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('pickup_timeslot_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('pickup_date','Pickup Date:<span class="text-danger">*</span> ')) !!}
                                    {{ Form::text('pickup_date', null, array( 'placeholder' => 'yyyy-mm-dd','autocomplete'=>'off','class' => 'form-control dpicker','onchange'=>'fn_delivery_date(this.value,3)')) }}
                                    @if ($errors->has('pickup_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('pickup_date')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                    {{ Form::text('delivery_date', null, array('readonly'=>'true', 'placeholder' => 'yyyy-mm-dd','autocomplete'=>'off','class' => 'form-control dpicker')) }}
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
                   
                       
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('pickup_address_id','Pickup Address: ')) !!}
                                    {!! Form::select('pickup_address_id',[0=>'Please Select Address'],$data->pickup_address_id, array('class'=> 'form-control')) !!}
                                    @if ($errors->has('pickup_address_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('pickup_address_id')."</span>"!!} 
                                    @endif
                                    {!! Form::hidden('area_id',null, array('class' => 'form-control','id'=>'area_id','readonly'=>'true')) !!}
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
    <!-- restricting past dates in delivery date -->
    <script type="text/javascript">
         var incre= 1;
        $(document).ready(function(){
            // restricting past dates
            var disableSpecificDates = fetch_holidays() ;
            $('.dpicker').datepicker({
                startDate: new Date(),
                format: 'yyyy-mm-dd',
                daysOfWeekDisabled: [0],
                beforeShowDay: function(date){
                    dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                    if(disableSpecificDates.indexOf(dmy) != -1){
                        return false;
                    }else{
                        return true;
                    }
                }
            });
        });

        // formating date
        function format_date(date) { 
            var day     = date.getDate(); 
            var month   = date.getMonth() + 1; 
            var year    = date.getFullYear(); 
            var myDate  = day + "-" + month + "-" + year; 
            return myDate;
        }

        // fetching holidays  
        function fetch_holidays(){
            var hDays       = new Array();
            var holidays    = {!! json_encode($holidays->toArray()) !!};
            holidays.forEach(function(rec,index) {
                hDays[index] = format_date(new Date(rec.holiday_date)); 
            }); 
            return hDays;
        }

        function fn_delivery_date(pickup_date,inc){
            this.incre= 0;
            for(var i=1; i<=inc; i++){
                // console.log(incre);
                this.incre++;
                calc_delivery_date(pickup_date,this.incre);
                
            }

        }
        
        // calc_delivery_date(new Date());
        function calc_delivery_date(pickup_date,incre){
            var today       = new Date(pickup_date);
            var finalDate   = new Date(today);

            finalDate.setDate(today.getDate() + this.incre);
            var temp        = new Date(finalDate);
            // console.log(temp);

            if( temp.getDay() == 0) {
                this.incre = this.incre +1;
                // console.log("getDay "+incre);
                calc_delivery_date(pickup_date,this.incre);
            }else{
                var check       = 0 ;
                var disableSpecificDates = fetch_holidays() ;
                year            = temp.getFullYear();
                day             = temp.getDate() ;
                month           = temp.getMonth();
                month           = month+1;
                day             = ('0' + day).slice(-2);
                month           = ('0' + month).slice(-2);
                var delivery_date = year+'-'+month+'-'+day;
                
                var test_date   = temp.getDate()+'-'+month+'-'+year; 
                disableSpecificDates.forEach(function(rec,index) {
                    if(rec == test_date){
                       check = 1;
                    }
                }); 
                if(check ==1){
                    this.incre = this.incre +1;
                    calc_delivery_date(pickup_date,this.incre);
                }else{
                    $('#delivery_date').val(delivery_date); 
                }
            }
        }
        
    </script>

    <!-- fetching customer detail by contact no -->
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
                    // console.log(data.data.id);
                    if(data.data){
                        $('#name').val(data.data.name);
                        $('#customer_id').val(data.data.id);
                        $('#permanent_note').val(data.data.permanent_note);
                        $("#pickup_address_id").html(data.customer_address);
                        var id = document.getElementById('pickup_address_id').value;  
                        get_lat_lng(id);
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

    
    <!-- 1. getting lat and lng of pickup address and   -->
    <!-- 2. calculating the center point of the areas -->
    <!-- 3. calculating the distance from center_point of an area to lat & lng of selected pickup address  -->
    <script type="text/javascript">
        function distance(lat1, lon1, lat2, lon2, unit) {
            if ((lat1 == lat2) && (lon1 == lon2)) {
                return 0;
            }
            else {
                var radlat1 = Math.PI * lat1/180;
                var radlat2 = Math.PI * lat2/180;
                var theta = lon1-lon2;
                var radtheta = Math.PI * theta/180;
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                if (dist > 1) {
                    dist = 1;
                }
                dist = Math.acos(dist);
                dist = dist * 180/Math.PI;
                dist = dist * 60 * 1.1515;
                if (unit=="K") { dist = dist * 1.609344 }
                if (unit=="N") { dist = dist * 0.8684 }
                return dist;
            }
        }
 
        function get_lat_lng($id){
            var dist = '';
            var data =[];
            var all_dist = [];
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('get_lat_lng') }}",
                method: 'POST',
                data: {id:$id, _token:token},
                success: function(data) {
                    if(data.data){
                        // console.log("id: " + data.data.id);
                        var cust_lat    = data.data.latitude;
                        var cust_lng    = data.data.longitude;

                        <?php 
                                foreach($areas as $key =>$value){ ?>
                                    data['id']      = <?php echo $key ?>;
                                    data['dist']    = <?php echo $value?>;
                                    dist            = distance(cust_lat, cust_lng, data['dist']['lat'], data['dist']['lng'], 'K');
                                    all_dist.push({'id': data['id'], 'distance':dist.toFixed(2)});
                        <?php } ?>
                        all_dist.sort(function(a, b){
                            return a.distance-b.distance
                        })
                        // console.log((all_dist));
                        $('#area_id').val("");
                        $('#area_id').val(all_dist[0]['id']);
                    }else{
                     
                    }
                }
            });
        }
        
    </script>

@endsection

