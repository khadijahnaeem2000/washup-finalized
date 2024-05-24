@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_script')

    <div class="row justify-content-center" style="margin-bottom: 20px;">
        <div class="col-lg-12">
        {!! Form::open(array('url' => 'export_hfq_items','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                <div class="card card-custom">
                    <!-- data-card="true" -->
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">HQF Order Item Report</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            
                            
                                <h4 style="margin-right: 5px">  Hub : </h4><span></span>
                                <div style="margin-right: 5px">
                                    {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id','style'=>'width: 200px !important')) !!}
                                </div>
                                <h4 style="margin-right: 5px"> From: </h4>
                                <div style="margin-right: 5px">
                                    <input type="date"  name = "from_date" id="from_date" class="form-control btn-sm" />
                                </div>
                                <h4 style="margin-right: 5px"> To: </h4>
                                <div style="margin-right: 5px">
                                    <input type="date" name = "to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                                <div style="margin-left: 5px">
                                    <button class="btn btn-success btn-sm font-weight-bolder"> <i class="la la-pdf"></i>Export CSV</button>
                                </div>
                            

                                <!-- <div style="margin-left: 5px">
                                    <a class="btn btn-success btn-sm font-weight-bolder" id ="btn_export_hfq" href="javascript:void(0)"> <i class="la la-pdf"></i>Export CSV</a>
                                </div> -->
                            
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
    {!! Form::open(array('route' => 'order_hfqs.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
        {{  Form::hidden('created_by', Auth::user()->id ) }}
        <div class="row">
            <div class="col-lg-12">

          

                <!--begin::Card-->
                <div class="card card-custom">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Inspect HFQ Order (Content)</h3>
                        </div>
                        <div class="card-toolbar">
                            <div style="margin-right: 10px">
                                <h4 style="text-align:center">  Distribution Hub </h4>
                                {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','autofocus' => '','required'=>'true','id'=>'hub_id')) !!}
                            </div>
                            
                        </div>
                    </div>
                    <?php $checkvar = false; ?>
                    @can('special_polybag-print')
                        <?php $checkvar = true; ?>
                    @endcan
                 
                    @if(!$checkvar)
                        <style>
                            .chk_prm{
                                display:none;
                            }
                        </style>
                    @endif
                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="2%" >#</th>
                                            <th>Order#</th>
                                            <th>Ref Order#</th>
                                            <th>Name</th>
                                            <th>Contact#</th>
                                            <th>Pickup Date</th>
                                            <th>Delivery Date</th>
                                            <th>Status</th>
                                            <th width="10%" >Inspect</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
    {!! Form::close() !!}


    <script>
        $(document).ready(function () {
           var hub_id          = document.getElementById('hub_id').value;  
           if(hub_id > 0){
               var order_table =  $('#myTable').DataTable({
                   "aaSorting": [],
                   "processing": true,
                   "serverSide": true,
                   "ajax": "{{ url('order_hfq_list') }}" +'/'+hub_id,
                   "method": "GET",
                   "columns": [
                        {"data": "srno"},
                        {"data": "id"},
                        {"data": "ref_order_id"},
                        {"data": "name"},
                        {"data": "contact_no"},
                        {"data": "pickup_date"},
                        {"data": "delivery_date"},
                        {"data": "status_name"},
                        {"data": "action",orderable:false,searchable:false}
                   ]
               });

             
              
               $('#hub_id').change(function () {
                   var hub_id          = document.getElementById('hub_id').value;  
                   order_table.ajax.url( 'order_hfq_list/'+hub_id ).load();
               });
           }else{
               $('#hub_id').append('<option value = "0">--- No Hub ---</option>');
           }

         

        });
    </script>
@endsection
