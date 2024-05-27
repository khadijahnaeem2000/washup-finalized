@extends('layouts.master')
@section('title','CSR Dashboard')
@section('content')

@include( '../sweet_script')
<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>

<script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
<script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script>
<!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGCJNMbn6TMfqsSMCI3ACMDz_SkTrAhSk&libraries=places,drawing"></script>  -->
<script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&callback=initMap&libraries=geometry" async></script> 
    <style type="text/css">
        #map {
            height: 0px;
            display: none;
            /* height: 100%; */
        }
        .custom-modal {
    width: 450px; /* Adjust the width as needed */
}

    </style>

 <!-- @include( '../sweet_script') -->
    <style type="text/css">
	
		.table th, .table td{
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
	</style>
    <!-- BEGIN::Summary -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div id="map"></div>
            <div id= "loaderDiv"><i class="fas fa-spinner fa-spin" style="position:absolute; left:50%; top:50%;font-size:80px; color:#3a7ae0"></i> </div>
            {!! Form::open(array('id'=>'summary_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom" data-card="true" >
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Summary</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->

                            <!-- <div class="input-icon" style="margin-left: 5px">
                                <a href="/assign_rider" class="btn btn-success font-weight-bolder">Assign Rider <i class="fas fa-check"></i></a>
                            </div> -->

                            <span style="margin-right: 15px; font-weight:bold">Plan for</span>  
                            <div class="input-icon" style="margin-right: 5px">
                                <input type="date" name = "plan_date" id="plan_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                            </div>
                        
                            <!-- Button trigger Pickup Order modal-->
                            <a class="btn btn-primary btn-sm font-weight-bolder" id ="plan_btn"href="javascript:void(0)" id=""> Search</a>

                            <!-- <div class="input-icon" style="margin-left: 5px">
                                <a href="/assign_rider" class="btn btn-success btn-sm font-weight-bolder">Assign Rider <i class="fas fa-check"></i></a>
                            </div> -->
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 text-left">   
                                <!-- <h4 style="color: red"> Note: </h4>
                                <ul style="color: red">  
                                    <li>This is not bound with hub-level</li>
                                    <li>On selecting "Plan for date", All order whose "pickup date" or "delivery date" is same as "Plan for date".</li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="row" id="summary_table">
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <br>
    <!-- END::Summary -->

    <!-- BEGIN::Order List -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'reg_order_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom card-collapsed" data-card="true" >
                <!-- <div class="card card-custom" data-card="true" > -->
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Orders</h3>
                        </div>
                        @can('order-create')
                            
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                                        
                                <!-- <div class="input-icon" style="margin-right: 5px">
                                    <input type="text" class="form-control " placeholder="Search..." id="kt_datatable_search_query" />
                                    <span>
                                        <i class="flaticon2-search-1 text-muted"></i>
                                    </span>
                                </div> -->
                            
                                <!-- Button trigger Pickup Order modal-->
                                <a class="btn btn-primary btn-sm font-weight-bolder" href="javascript:void(0)" id="add_new_order"> Add new pickup</a>

                                <span style="margin-right: 5px"></span>
                                <button type="submit" class="btn btn-primary btn-sm font-weight-bolder"  name="reg_order_reschedule_btn" id ="reg_order_reschedule_btn" >
                                    <i class="flaticon2-reload" ></i>Re-Schedule
                                </button>
                                {!! Form::hidden('re_schedule', 're_schedule', array('class' => 'form-control')) !!}
                                <span style="margin-right: 5px"></span>
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm font-weight-bolder" id="finalize_orders_btn">
                                    <i class="flaticon2-check-mark"></i>Finalize Orders
                                </a>
                                <span></span>
                                <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                    <i class="ki ki-arrow-down icon-nm"></i>
                                </button>
                                <!--end::Button-->
                            </div>
                        @endcan
                    </div>
                    

                    <div class="card-body">
                        {!! Form::hidden('dt', '', array('class' => 'form-control','id'=>'dt')) !!}
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="reg_order_table" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Odr#</th>
                                        <th>Ref#</th>
                                        <th>Customer</th>
                                        <th>Contact#</th>
                                        <th>Pickup Date</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th>Timeslot</th>
                                        <th title="Note">
                                            <i class="flaticon2-open-text-book"></i>
                                        </th>
                                        <th title="Order Packed">
                                            <i class="fas fa-box"></i>
                                        </th>
                                        <th title="Order Action">
                                            <i class="flaticon2-checkmark"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <br>
    <!-- END::Order List -->


    <!-- BEGIN::Cancelled Order List -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'cancel_order_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom card-collapsed" data-card="true" >
                <!-- <div class="card card-custom" data-card="true" > -->
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Cancelled Orders</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bolder"  name="cancel_order_reschedule_btn" id ="cancel_order_reschedule_btn" >
                                <i class="flaticon2-reload" ></i>Re-Schedule
                            </button>
                            {!! Form::hidden('re_schedule', 're_schedule', array('class' => 'form-control')) !!}
                                <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    

                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="cancel_order_table" class="table" style="width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="2%" ></th>
                                            <th>Odr#</th>
                                            <th>Customer</th>
                                            <th>Contact#</th>
                                            <th>Pickup Date</th>
                                            <th>Delivery Date</th>
                                            <th>Status</th>
                                            <th>Timeslot</th>
                                            <th title="Note">
                                                <i class="flaticon2-open-text-book"></i>
                                            </th>
                                            <th title="Order Packed">
                                                <i class="fas fa-box"></i>
                                            </th>
                                            <th title="Order Action">
                                                <i class="flaticon2-checkmark"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                <tbody>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <br>
    <!-- END::Cancelled Order List -->


     <!-- BEGIN::HFQ Order List -->
     <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'hfq_order_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom card-collapsed" data-card="true" >
                <!-- <div class="card card-custom" data-card="true" > -->
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">HFQ Orders</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bolder"  name="hfq_order_reschedule_btn" id ="hfq_order_reschedule_btn" >
                                <i class="flaticon2-reload" ></i>Re-Schedule
                            </button>
                            {!! Form::hidden('re_schedule', 're_schedule', array('class' => 'form-control')) !!}
                                <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    

                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="hfq_order_table" class="table" style="width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="2%" ></th>
                                            <th>Odr#</th>
                                            <th>Customer</th>
                                            <th>Contact#</th>
                                            <th>Pickup Date</th>
                                            <th>Delivery Date</th>
                                            <th>Status</th>
                                            <th>Timeslot</th>
                                            <th title="Note">
                                                <i class="flaticon2-open-text-book"></i>
                                            </th>
                                            <th title="Order Packed">
                                                <i class="fas fa-box"></i>
                                            </th>
                                            <th title="Order Action">
                                                <i class="flaticon2-checkmark"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                <tbody>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <br>
    <!-- END::HFQ Order List -->
    

    <!-- BEGIN:: Payment only Rides -->
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            {!! Form::open(array('id'=>'payment_order_form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom card-collapsed" data-card="true" >
                <!-- <div class="card card-custom" data-card="true" > -->
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Payment Only Rides</h3>
                        </div>
                        @can('order-create')
                            
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                                        
                                <span style="margin-right: 5px"></span>
                                <button type="submit" class="btn btn-primary btn-sm font-weight-bolder"  name="payment_ride_btn" id ="payment_ride_btn" >
                                    <i class="flaticon2-reload" ></i>Schedule
                                </button>
                                
                                <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                    <i class="ki ki-arrow-down icon-nm"></i>
                                </button>
                                <!--end::Button-->
                            </div>
                        @endcan
                    </div>
                    

                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="payment_order_table" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%" ></th>
                                        <th width="3%" >#</th>
                                        <th width="15%" >Customer</th>
                                        <th width="10%" >Contact#</th>
                                        <th width="20%" >Address</th>
                                        <th width="10%" >Bill</th>
                                        <th width="15%" >Ride Date</th>
                                        <th width="15%" >Timeslot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                       
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!--end::Card-->
        </div>
    </div>
    <br>
    <!-- END:: Payment only Rides -->

    <!-- BEGIN::Modal form Order -->
    <div class="modal fade" id="order_ajax_model" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(array('id'=>'order_form','enctype'=>'multipart/form-data')) !!}
                        {{  Form::hidden('created_by', Auth::user()->id ) }}
                        {{  Form::hidden('updated_by', Auth::user()->id ) }}
                        {{  Form::hidden('order_id', null, array('id'=>'order_id' )) }}
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('contact_no','Account/Contact No: <span class="text-danger">*</span>')) !!}
                                        {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control cnt_no' ,'autofocus' => '' ,'required'=>'true')) }}
                                        @if ($errors->has('contact_no'))  
                                            {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                        @endif
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                    {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                    {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' ,'required'=>'true')) }}
                                        @if ($errors->has('name'))  
                                            {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                        @endif
                                    {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true','required'=>'true' )) }}
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row"> 
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('status_id','Status')) !!}
                                        {!! Form::select('status_id',$statuses,null, array('class' => 'form-control')) !!}
                                        @if ($errors->has('status_id'))  
                                            {!! "<span class='span_danger'>". $errors->first('status_id')."</span>"!!} 
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('timeslot_id','Pickup Time Slot: ', array('id'=>'t_slot'))) !!}
                                        {!! Form::select('timeslot_id', $time_slots,null, array('class'=> 'form-control')) !!}
                                        @if ($errors->has('timeslot_id'))  
                                            {!! "<span class='span_danger'>". $errors->first('timeslot_id')."</span>"!!} 
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('pickup_date','Pickup Date:<span class="text-danger">*</span> ')) !!}
                                        {{ Form::text('pickup_date', null, array( 'placeholder' => 'yyyy-mm-dd','class' => 'form-control dpicker','autocomplete'=>'off','onchange'=>'fn_delivery_date(this.value,3)','required'=>'true','readonly'=>'true')) }}
                                        @if ($errors->has('pickup_date'))  
                                            {!! "<span class='span_danger'>". $errors->first('pickup_date')."</span>"!!} 
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                        {{ Form::text('delivery_date', null, array('readonly'=>'true', 'placeholder' => 'yyyy-mm-dd','autocomplete'=>'off','class' => 'form-control dpicker','required'=>'true','readonly'=>'true')) }}
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
                                        {!! Form::textarea('permanent_note', null, array('placeholder' => 'Permanent Note','rows'=>2, 'class' => 'form-control','readonly' => 'true' )) !!}
                                        @if ($errors->has('permanent_note'))  
                                            {!! "<span class='span_danger'>". $errors->first('permanent_note')."</span>"!!} 
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('order_note','Order Note: ')) !!}
                                        {!! Form::textarea('order_note', null, array('placeholder' => 'Order Note','rows'=>2, 'class' => 'form-control')) !!}
                                        @if ($errors->has('order_note'))  
                                            {!! "<span class='span_danger'>". $errors->first('order_note')."</span>"!!} 
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Html::decode(Form::label('cus_address_id','Pickup Address: ', array('id'=>'p_address'))) !!}
                                        {!! Form::select('cus_address_id',[0=>'Please Select Address'],null, array('class'=> 'form-control','onchange'=>'get_customer_lat_lng(this.value)')) !!}
                                        @if ($errors->has('cus_address_id'))  
                                            {!! "<span class='span_danger'>". $errors->first('cus_address_id')."</span>"!!} 
                                        @endif
                                        {!! Form::hidden('area_id',null, array('class' => 'form-control','id'=>'area_id','readonly'=>'false')) !!}
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                       
                                   <div class="form-group">
                                       {!! Html::decode(Form::label('email','Email Address <span class="text-danger">*</span>')) !!}
                                       {{ Form::email('email', null, array('placeholder' => 'Enter email address','class' => 'form-control','readonly' => 'true' )) }}
                                        @if ($errors->has('email'))  
                                          {!! "<span class='span_danger'>". $errors->first('email')."</span>"!!} 
                                        @endif
                                   </div> 
                            
                                </div>
                             @can('waive')
                              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                       {!! Html::decode(Form::label('waver_delivery','Waive Delivery <span class="text-danger">*</span>')) !!}
                                      <span class="switch switch-outline switch-icon switch-primary">
                                                <label>
                                                  {!! Form::checkbox('waver_delivery',1,false,  array('class' => 'form-control')) !!}
                                                   <span></span>
                                               </label>
                                       </span>
                                
                                        @if ($errors->has('waver_delivery'))  
                                        {!! "<span class='span_danger'>". $errors->first('waver_delivery')."</span>"!!} 
                                    @endif
                                </div>
                              
                                </div>
                                  @endcan
                            </div>
                  

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                            <button type="submit" id="save_btn" class="btn btn-primary font-weight-bold" value="Add order">Save</button>
                            <button type="submit" id="edit_btn" class="btn btn-primary font-weight-bold" value="Edit order">Update</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- END::Modal form Order -->


    <!-- BEGIN::AJAX CRUD Order -->
    <script type="text/javascript">
        $(document).ready(function () { 

            // BEGIN::initialization of datatable
            var reg_order_table = $('#reg_order_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [4,5,6,7,8,9,10],
                "orderable": false
                } ]
            });

            var cancel_order_table = $('#cancel_order_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [0,4,5,7,10],
                "orderable": false
                } ]
            });

            var hfq_order_table = $('#hfq_order_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [0,4,5,7,10],
                "orderable": false
                } ]
            });

            var payment_order_table = $('#payment_order_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [0,6,7],
                "orderable": false
                } ]
            });
            // END::initialization of datatable
           
        });

        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // BEGIN:: add order modal: Ajax
            $('#add_new_order').click(function () {
                $("#edit_btn").hide();
                $("#save_btn").show();
                $('#save_btn').val("Add order");
                $('#order_form').trigger("reset");
                $('#modelHeading').html("Add new Order");
                $('#order_ajax_model').modal('show');
                $("#contact_no").prop("readonly", false);
                $('#cus_address_id').find('option').remove();
            });
            // END:: add order modal: Ajax

            
            // BEGIN:: edit modal form: Ajax
             $('#edit_btn').click(function (e) {
                e.preventDefault();
                $(this).html('Sending..');
                var url  = "{{ route('csr_dashboards.index') }}" +'/update_order/';
                storing(url);
            });
            // END:: edit modal form: Ajax


            // BEGIN:: store modal form: Ajax
            $('#save_btn').click(function (e) {
                e.preventDefault();
                $(this).html('Sending..');
                var url  = "{{ route('csr_dashboards.index') }}" +'/add_order/';
                storing(url);
            });
            // END:: store modal form: Ajax


            // BEGIN:: edit order modal: Ajax
            $('body').on('click', '.edit_order', function () {
                var order_id = $(this).data('id');
                $("#save_btn").hide();
                $("#edit_btn").show();
                // console.log('order_id: ' + order_id);
                $.get("{{ route('csr_dashboards.index') }}" +'/edit_order/' + order_id, function (data) {
                    $('#modelHeading').html("Edit Order");
                    $('#edit_btn').val("edit order");
                    $('#order_ajax_model').modal('show');
                    $('#order_id').val(data.id);
                    $('#contact_no').val(data.contact_no);
                    $("#contact_no").prop("readonly", true);
                    $('#name').val(data.name);
                   $('#waver_delivery').prop('checked', data.waver_delivery);
                    $('#email').val(data.email);
                    show_details(data.contact_no);  // calling show_detail fn to get customer details and addresses
                    $('#status_id').val(data.status_id);
                   
                    
                    // console.log("id: " + data.status_id );
                    if((data.status_id) == 1 ){
                        $('#t_slot').html("Pickup Timeslot");
                        $('#p_address').html("Pickup Address");
                        $("#pickup_date").removeAttr("disabled");
                        // $("#pickup_date").prop('disabled', false);
                        $('#status_id').find('option').remove();
                        $('#status_id').append("<option value='1' selected>Pickup</option>")
                        $('#cus_address_id').val(data.pickup_address_id);
                        $('#timeslot_id').val(data.pickup_timeslot_id);
                    }else{
                        $('#t_slot').html("Delivery Timeslot");
                        $('#p_address').html("Delivery Address");
                        $('#pickup_date').prop('disabled', true);
                        $('#status_id').find('option').remove();
                        $('#status_id').append("<option value='2'>Drop off</option><option value='3'>Pick & Drop</option>")
                        $('#status_id').val(data.status_id);
                        $('#cus_address_id').val(data.delivery_address_id);
                        $('#timeslot_id').val(data.delivery_timeslot_id);
                    }
                    $('#pickup_date').val(data.pickup_date);
                    $('#delivery_date').val(data.delivery_date);
                    $('#permanent_note').val(data.permanent_note);
                    $('#order_note').val(data.order_note);
                    $('#area_id').val(data.area_id);
                })
            });
            // END:: edit order modal: Ajax


            // BEGIN:: edit HFQ order modal: Ajax
            $('body').on('click', '.edit_hfq_order', function () {
                var order_id = $(this).data('id');
                $("#save_btn").hide();
                $("#edit_btn").show();
                // console.log('order_id: ' + order_id);
                $.get("{{ route('csr_dashboards.index') }}" +'/edit_order/' + order_id, function (data) {
                    $('#modelHeading').html("Edit HFQ Order");
                    $('#edit_btn').val("edit order");
                    $('#order_ajax_model').modal('show');
                    // console.log(data.id);
                    $('#order_id').val(data.id);
                    $('#contact_no').val(data.contact_no);
                    $("#contact_no").prop("readonly", true);
                    $('#name').val(data.name);
                    show_details(data.contact_no);  // calling show_detail fn to get customer details and addresses
                    $('#status_id').val(data.status_id);
                   
                    
                    // console.log("id: " + data.status_id );
                    if((data.status_id) == 1 ){
                        $('#t_slot').html("Pickup Timeslot");
                        $('#p_address').html("Pickup Address");
                        $("#pickup_date").removeAttr("disabled");
                        // $("#pickup_date").prop('disabled', false);
                        $('#status_id').find('option').remove();
                        $('#status_id').append("<option value='1' selected>Pickup</option>")
                        $('#cus_address_id').val(data.pickup_address_id);
                        $('#timeslot_id').val(data.pickup_timeslot_id);
                    }else{
                        $('#t_slot').html("Delivery Timeslot");
                        $('#p_address').html("Delivery Address");
                        $('#pickup_date').prop('disabled', true);
                        $('#status_id').find('option').remove();
                        $('#status_id').append("<option value='17'>HFQ</option><option value='2'>Drop off</option><option value='3'>Pick & Drop</option>")
                        $('#status_id').val(data.status_id);
                        $('#cus_address_id').val(data.delivery_address_id);
                        $('#timeslot_id').val(data.delivery_timeslot_id);
                    }
                    $('#pickup_date').val(data.pickup_date);
                    $('#delivery_date').val(data.delivery_date);
                    $('#permanent_note').val(data.permanent_note);
                    $('#order_note').val(data.order_note);
                    $('#area_id').val(data.area_id);
                })
            });
            // END:: edit HFQ order modal: Ajax

            
            // BEGIN:: delete order modal: Ajax
            $('body').on('click', '.delete_order', function () {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                var order_id = $(this).data('id');
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('csr_dashboards.index') }}"+'/delete_order/'+order_id,
                            success: function (data) {
                                toastr.success(data.success);
                                // location.reload();
                                re_draw_all();
                            },
                            error: function (data) {
                                console.log('Error:', data);
                                  toastr.error(txt);
                            }
                        });
                    }else if (result.dismiss === Swal.DismissReason.cancel){
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            'Your imaginary data is safe :)',
                            'error'
                        )
                    }
                });
            });
            // END:: delete order modal: Ajax
            

            // BEGIN:: re-schedule regular orders
            $('#reg_order_reschedule_btn').click(function (e) {
                e.preventDefault();
                var dt      = document.getElementById('plan_date').value;  
                $("#loaderDiv").show();
                $("#summary_table").html("");
                $("#summary_table").append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                
                // reschedulling regular orders
                var fn      = 'fetch_reg_orders';
                var tbl     = 'reg_order_table';
                var url     = "reschedule_reg_orders";
                var form_id = '#reg_order_form';
                re_schedule(url,form_id,dt,tbl,fn);

                // reschedulling cancel orders
                var fn      = 'fetch_cancel_orders';
                var tbl     = 'cancel_order_table';
                fn_redraw_table(dt,tbl,fn);

                var tbl    = 'summary_table';
                var fn     = 'fetch_summary_orders';
                fn_redraw_table(dt,tbl,fn);
            });
            // END:: re-schedule regular orders
            

            $('#finalize_orders_btn').click(function (e) {
                e.preventDefault();

                var dt      = document.getElementById('plan_date').value;  
                
                $("#finalize_orders_btn").attr("disabled", true);
                
                $("#loaderDiv").show();

                // finalize regular orders
                var fn      = 'fetch_reg_orders';
                var tbl     = 'reg_order_table';

                var url     = "finalize_orders";
                var form_id = '#reg_order_form';
                fn_finalize_orders(url,form_id,dt,tbl,fn);

                
                

            });
            // BEGIN:: re-schedule cancel orders
            $('#cancel_order_reschedule_btn').click(function (e) {
                e.preventDefault();
                var dt      = document.getElementById('plan_date').value;  

                 // reschedulling cancel orders
                var fn      = 'fetch_cancel_orders';
                var tbl     = 'cancel_order_table';
                var url     = 'reschedule_cancel_orders';
                var form_id = '#cancel_order_form';
                re_schedule(url,form_id,dt,tbl,fn);

                // reschedulling regular orders
                var fn      = 'fetch_reg_orders';
                var tbl     = 'reg_order_table';
                fn_redraw_table(dt,tbl,fn);

                var tbl    = 'summary_table';
                var fn     = 'fetch_summary_orders';
                fn_redraw_table(dt,tbl,fn);
            });
            // END:: re-schedule cancel orders

            // BEGIN:: re-schedule cancel orders
            $('#hfq_order_reschedule_btn').click(function (e) {
                e.preventDefault();
                var dt      = document.getElementById('plan_date').value;  

                 // reschedulling cancel orders
                var fn      = 'fetch_hfq_orders';
                var tbl     = 'hfq_order_table';
                var url     = 'reschedule_hfq_orders';
                var form_id = '#hfq_order_form';
                re_schedule(url,form_id,dt,tbl,fn);

                // reschedulling regular orders
                var fn      = 'fetch_reg_orders';
                var tbl     = 'reg_order_table';
                fn_redraw_table(dt,tbl,fn);

                var tbl    = 'summary_table';
                var fn     = 'fetch_summary_orders';
                fn_redraw_table(dt,tbl,fn);
            });
            // END:: re-schedule cancel orders


            // BEGIN:: schedule payment orders
            $('#payment_ride_btn').click(function (e) {
                e.preventDefault();
                var cus_url = "{{ route('csr_dashboards.index') }}" +'/schedule_payment_ride/';
                var form_id = '#payment_order_form';
                $.ajax({
                    data: $('#payment_order_form').serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.success){
                            re_draw_all();
                            // location.reload(); // reload the page
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
                        console.log('Error:', data);
                    }
                });
            });
            // END:: schedule payment orders

           
            // BEGIN:: fn storing to add or update the order data 
            function storing(cus_url){
                var area_id = $('#area_id').val();
                   
                if(area_id == ""){
                    console.log("blank");
                    $('#save_btn').html('Save');
                    $('#edit_btn').html('Update');
                    alert("This address is out of our boundery");
                }else{
                    $("#loaderDiv").show();
                    $.ajax({
                        data: $('#order_form').serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data.success){
                                $('#order_form').trigger("reset");
                                $('#order_ajax_model').modal('hide');
                                
                                re_draw_all(); 
                                toastr.success(data.success);
                                $('#save_btn').html('Save');
                                $('#edit_btn').html('Update');
                            }else{
                                var txt = '';
                                var count = 0 ;
                                $.each(data.error, function() {
                                    txt +=data.error[count++];
                                    txt +='<br>';
                                });
                                toastr.error(txt);
                                $('#save_btn').html('Save');
                                $('#edit_btn').html('Update');
                            }
                            $("#loaderDiv").hide();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            $("#loaderDiv").hide();
                            $('#save_btn').html('Save');
                        }
                    });
                }
            }
            // END:: fn storing to add or update the order data 

            function fn_finalize_orders(cus_url,form_id,dt,tbl,fn){
                $.ajax({
                    data: $(form_id).serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $("#loaderDiv").hide();
                        if(data.success){
                            // fn_redraw_table(dt,tbl,fn);
                            re_draw_all();
                            toastr.success(data.success);
                           
                        }else{
                            $("#finalize_orders_btn").attr("disabled", false);
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
                        $("#finalize_orders_btn").attr("disabled", false);
                        console.log('Error:', data);
                        $("#loaderDiv").hide();
                    }
                });
            }

            // fn re-schedulling 
            function re_schedule(cus_url,form_id,dt,tbl,fn){
                setTimeout(() => {
                    $.ajax({
                        data: $(form_id).serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data.success){
                                fn_redraw_table(dt,tbl,fn);
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
                            console.log('Error:', data);
                        }
                    });
                }, 1500);
              
            }
            
        });
    </script>
    <!-- END::AJAX CRUD Order -->

    <!-- BEGIN::restricting past dates in delivery date -->
    <script type="text/javascript">
         var incre= 0;
        $(document).ready(function(){
            // restricting past dates
            var disableSpecificDates = fetch_holidays() ;
            $('.dpicker').datepicker({
                startDate: new Date(),
                format: 'yyyy/mm/dd',
                // daysOfWeekDisabled: [0],
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
            // console.log("pickup-date");
            // console.log(pickup_date);
            this.incre= 0;
            for(var i=1; i<=inc; i++){
                this.incre++;
                // console.log(this.incre);
                calc_delivery_date(pickup_date,(this.incre));
                
            }

        }
        
        // calc_delivery_date(new Date());
        function calc_delivery_date(pickup_date,incre){
            var today       = new Date(pickup_date);
            var finalDate   = new Date(today);
           
            finalDate.setDate(today.getDate() + (this.incre));
            var temp        = new Date(finalDate);
        

            if( temp.getDay() == 0) {
                this.incre = this.incre +1;
                // console.log("getDay "+incre);
                calc_delivery_date(pickup_date,(this.incre));
            }else{
                var check                   = 0 ;
                var disableSpecificDates    = fetch_holidays() ;
                year                        = temp.getFullYear();
                day                         = temp.getDate() ;
                month                       = temp.getMonth();
                month                       = month+1;
                // day             = ('0' + day).slice(-2);
                // month           = ('0' + month).slice(-2);
                var delivery_date = year+'/'+month+'/'+day;
                
                var test_date   = temp.getDate()+'-'+month+'-'+year; 
                disableSpecificDates.forEach(function(rec,index) {
                    if(rec == test_date){
                       check = 1;
                    }
                }); 

                if(check == 1){
                    this.incre = this.incre +1;
                    
                    calc_delivery_date(pickup_date,this.incre);
                  
                }else{
                    // console.log(delivery_date);
                    $('#delivery_date').val(delivery_date); 
                }
            }
        }
        
    </script>
    <!-- END::restricting past dates in delivery date -->

    <!-- BEGIN::fetching customer detail by contact no -->
    <script type="text/javascript">
       $(document).ready(function(){
           $(".cnt_no").keyup(function(e){
                e.preventDefault();
                if (!e.ctrlKey) {
                   show_details($(this).val());
                }
           })
            // var contact_no = document.getElementById('contact_no').value;  
            // show_details(contact_no);
        });
           // start khadeeja's edit 
     function show_details($contact_no) {
      var token = $("input[name='_token']").val();
      $.ajax({
         url: "{{ url('fetch_customer_details') }}",
         method: 'POST',
         data: { contact_no: $contact_no, _token: token },
         success: function(data) {
            if (data.data) {
               $('#name').val(data.data.name);
               $('#email').val(data.data.email);
               $('#customer_id').val(data.data.id);
               $('#permanent_note').val(data.data.permanent_note);
               $("#cus_address_id").html(data.customer_address);
               var id = document.getElementById('cus_address_id').value;
               setTimeout(() => {
                  get_customer_lat_lng(id);
               }, 500);
               $email = data.data.email;
               var Idcustomer = data.data.id;
               // Check if email is missing
               if (!data.data.email) {
                  $("#loaderDiv").hide();
                  // Show error message with two options
                  Swal.fire({
                     title: 'Are you sure?',
                     html: '<div style="font-weight: 14px;font-size:14px">Customer will not receive Digital Invoice!</div>',
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#3085d6',
                     cancelButtonColor: '#d33',
                     confirmButtonText: 'Yes, proceed!',
                     cancelButtonText: 'No, Add Email!',
                  }).then((result) => {
                     console.log(result);
                     if (result.isConfirmed) {
                        // Proceed with the operation (Save the order)
                        var url = "{{ route('csr_dashboards.index') }}" + '/add_order/';
                        storing(url);
                     } else {
                        var url = "{{ url('customers/:customer_id/edit') }}";
                                 url = url.replace(':customer_id', Idcustomer);
                                 window.location.href = url;
                     }
                  });
               } else if (data.data.email) {
                  $.ajax({
                     url: "{{ url('fetch_email_details') }}",
                     method: 'POST',
                     data: { contact_no: $contact_no, _token: token },
                     success: function(data) {
                        if (data.data) {
                           console.log("success");
                           console.log(data.data);
                        } else {
                           var customer_id = data.error;
                           $("#loaderDiv").hide();
                          Swal.fire({
    title: 'Are you sure?',
    html: '<div style="font-weight: 14px;font-size:14px">Email alerts are turned off.<br>Customer will not receive Digital Invoice!</div>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, proceed!',
    cancelButtonText: 'No, turn on alerts!',
    customClass: {
        popup: 'custom-modal'
    }
}).then((result) => {
    console.log(result);
    if (result.isConfirmed) {
        // Proceed with the operation (Save the order)
        var url = "{{ route('csr_dashboards.index') }}" + '/add_order/';
        storing(url);
    } else {
        var url = "{{ url('customers/:customer_id/edit') }}";
        url = url.replace(':customer_id', customer_id);
        window.location.href = url;
    }
});


                        }
                     }
                  })
               }

            } else {
               $('#name').val('');
               $('#customer_id').val('');
               $('#permanent_note').val('');
               $("#cus_address_id").html('<option>Please Select Address</option>');
            }
         }
      });
     }
     // end khadeeja's edit
           
        
    </script>

    
    <!-- re-draw-order table -->
    <script type="text/javascript">
        $('#plan_btn').click(function (e) {
            e.preventDefault();
            re_draw_all();
               
        });
        function re_draw_all(){
            $("#loaderDiv").show();
            
            $("#summary_table").html("");
            $("#summary_table").append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");


            var dt     = document.getElementById('plan_date').value;  
            var tbl    = 'reg_order_table';
            var fn     = 'fetch_reg_orders';
            fn_redraw_table(dt,tbl,fn);

            var fn      = 'fetch_cancel_orders';
            var tbl     = 'cancel_order_table';
            fn_redraw_table(dt,tbl,fn);

            var tbl    = 'payment_order_table';
            var fn     = 'fetch_payment_orders';
            fn_redraw_table(dt,tbl,fn);

            var tbl    = 'hfq_order_table';
            var fn     = 'fetch_hfq_orders';
            fn_redraw_table(dt,tbl,fn);

            var tbl    = 'summary_table';
            var fn     = 'fetch_summary_orders';
            fn_redraw_table(dt,tbl,fn);
        }

    
        function fn_redraw_table(dt,tbl,fn){
          
                if( tbl  == "summary_table"){
                    $("#loaderDiv").show();
                            
                    $("#summary_table").html("");
                    $("#summary_table").append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                    var token  = $("input[name='_token']").val();
                    setTimeout(() => {
                        $.ajax({
                            url: fn,
                            method: 'POST',
                            data: {dt:dt, _token:token},
                            // dataType: 'json',
                            beforeSend:function () {
                                // $("#loaderDiv").show();
                            
                                // $("#summary_table").html("");
                                // $("#summary_table").append("<h2 style='text-align:center; padding:10px'> Please wait ....  </h2>");
                            },

                            success: function (data) {
                                
                            
                                if(data.details){
                                    // console.log(data.details);
                                    $("#summary_table").html("");
                                    $("#summary_table").html(data.details);
                                
                                }else{
                                    $("#"+tbl).html("");
                                    $("#"+tbl).append("<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2>");
                                }
                                $("#loaderDiv").hide();
                            },
                            error: function (data) {
                                
                                $("#loaderDiv").hide();
                                console.log('Error:', data);
                            }
                        });
                    }, 2000);
                }else{
                    setTimeout(() => {
                        $("#"+tbl+" > tbody").html("");
                        var token = $("input[name='_token']").val();
                        $.ajax({
                            url: fn,
                            method: 'POST',
                            data: {dt:dt, _token:token},
                            success: function(data) {
                            
                                if(data.data){
                                    var rot = $('#'+tbl).DataTable();
                                    rot.clear().draw();
                                    rot.rows.add($(data.details));
                                    rot.draw();
                                    if(tbl== "reg_order_table"){
                                        $('#dt').val(data.dt);
                                        // console.log(data.dt);
                                        // all_reg_orders = $("#reg_order_form").serialize();
                                    }
                                    // $("#"+tbl).find('tbody').html(data.details);
                                }else{
                                    $("#loaderDiv").hide();
                                    console.log("sorry");
                                    // $("#cus_address_id").html('<option>Please Select Address</option>');
                                }
                                // $("#loaderDiv").hide();
                            }
                        });
                    }, 1500);
                }
           
           
        }
        
    </script>
    <!-- END::fetching customer detail by contact no -->


    <!-- 1. getting lat and lng of pickup address and   -->
    <!-- 2. calculating the center point of the areas -->
    <!-- 3. calculating the distance from center_point of an area to lat & lng of selected pickup address  -->
    <script type="text/javascript">
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: {"lat":24.77493792597761,"lng":67.07855585794911},
                zoom: 14,
            });
        }

 
        function get_customer_lat_lng($id){
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('get_customer_lat_lng') }}",
                method: 'POST',
                data: {id:$id, _token:token},
                success: function(data) {
                    if(data.data){
                        // console.log("id: " + data.data.id);
                        var cust_lat        = data.data.latitude;
                        var cust_lng        = data.data.longitude;
                        var area            = "";
                        var point           = "";
                        var rst             = "";
                        var area_id         = "";
                        var ppoints         = "";
                        var fnd_area_id     = 0;
                        $('#area_id').val("");
                        <?php 
                            foreach($areas as $key =>$value){ ?>
                                area_id     = <?php echo $key ?>;
                                ppoints     = <?php echo $value?>;                                
                            
                                area        = new google.maps.Polygon({ paths: ppoints });

                                point       = new google.maps.LatLng(cust_lat, cust_lng);

                                rst         = google.maps.geometry.poly.containsLocation(
                                                    point,
                                                    area
                                                )
                                                ? "1" //  exist
                                                : "0"; // not exist

                            if(rst  == 1){
                                fnd_area_id  = area_id;
                                // console.log("found in area_id: " + area_id);
                                $('#area_id').val(fnd_area_id);
                            }

                            <?php 
                        } ?>
                      
                      
                        if(fnd_area_id == 0){
                            // $('#order_form').trigger("reset");
                            alert("This address is out of our boundery");
                        }
                    }
                }
            });
        }
        
    </script>

   
@endsection
