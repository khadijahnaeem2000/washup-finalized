@extends('layouts.master')
@section('title','Order Details')
@section('content')

    <style>
        @media print
        {    
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
    </style>

    <div class="row" id="main">
        <div class="col-lg-12">
            <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">@yield('title')</h3>
                    </div>
                    <div class="card-toolbar d-print-none" >
                        <div class="btn-group btn-group">
                            <a  href="{{ route('order_verifies.index') }}" class="btn btn-primary btn-sm ">
                            <i class="fas fa-arrow-left"></i></a>
                            <button    class="btn btn-info btn-sm "  onclick="printDiv('main')">
                            <i class="fa fa-print"></i></button>
                        </div>
                    </div>
                </div>
              

                <div class="card-body">
                
                    <div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Full Name:</label>
                                <h4>{{$data->name}}</h4>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Account :</label>
                                <h4>{{$data->contact_no}}</h4>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Order No  :</label>
                                <h4>{{$data->id}}</h4>
                            </div>
                           
                        </div>

                        <div class="form-group row">
                            

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Permanent Note :</label>
                                <h4>{{$data->permanent_note}}</h4>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Order Note  :</label>
                                <h4>{{$data->order_note}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Rider Note  :</label>
                                <h4>{{ $data->rider_note }}</h4>
                            </div>
                        </div>
                        <div class="form-group row">
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Status  :</label>
                                <h4>{{ $data->status_name }}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Pickup Date:</label>
                                <h4> {{$data->pickup_date}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Delivery Date:</label>
                                <h4> {{$data->delivery_date}}</h4>
                            </div>
                        </div>
                        <div class="form-group row">
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Waive Delivery  :</label>
                                @if($data->waver_delivery === 1)
                                <h4>Yes</h4>
                                @else
                                 <h4>No</h4>
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Phase/Screen  :</label>
                                @if($data->phase)
                                <h4>{{$data->phase}}</h4>
                                @else
                                 <h4></h4>
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Waive Delivery Date:</label>
                                @if($data->DW_when)
                                <h4>{{$data->DW_when}}</h4>
                                @else
                                 <h4></h4>
                                @endif
                            </div>
                       
                        </div>
                            <div class="form-group row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Waive Delivery By Whom:</label>
                                @if($data->order_DW_who)
                                <h4>{{$data->order_DW_who}}</h4>
                                @else
                                 <h4></h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            @if( (isset($selected_items)) && (isset($selected_services)))
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Item Details</h3>
                    </div>
                    </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                <tbody>
                                            @if($selected_items)
                                            <?php
                                                $id = $selected_items[0]->service_id;
                                                foreach($selected_services as $skey =>$svalue) {
                                                    if($id == $svalue->service_id){
                                                        $weight = $svalue->weight;
                                                    }
                                                }
                                            ?>

                                                <tr>
                                                    <th colspan=5><center> <h4>Service: {{ $selected_items[0]->service_name }} </h4> <span >Weight: {{ $weight }} KG </span></center></th>
                                                </tr>
                                                <tr>
                                                    <th width="35%">Item Name</th>
                                                    <th width="10%">Item Qty</th>
                                                    <th width="25%">Addon </th>
                                                    <!-- <th width="20%">Image </th> -->
                                                    <th width="30%">Note </th>
                                                </tr>
                                                

                                                @foreach($selected_items as $value)
                                                    <?php if ($id == $value->service_id){ ?>
                                                        <tr>
                                                            <td>{{ $value->item_name }}</td>
                                                            <td>{{ $value->pickup_qty }}</td>
                                                            
                                                            <td>
                                                                @foreach($selected_addons as $add_value)
                                                                    @if(($value->ord_itm_id == $add_value->ord_itm_id))
                                                                        <label class="badge badge-success">{{ $add_value->addon_name }}</label>
                                                                    @endif
                                                                @endforeach
                                                            </td>

                                                            <!-- <td>
                                                                @if($value->item_image)
                                                                    <img src="{{ asset('uploads/orders/'.$value->item_image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);" height="64" width="64">
                                                                @else
                                                                {{ 'No Image' }}
                                                                @endif
                                                            </td> -->
                                                            <td>{{ $value->note }}</td>
                                                        </tr>
                                                    <?php }else{
                                                        $id = $value->service_id;
                                                        foreach($selected_services as $skey =>$svalue) {
                                                            if($id == $svalue->service_id){
                                                                $weight = $svalue->weight;
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <th  colspan=5><center> <h4> Service: {{ $value->service_name }}</h4> <span>Weight: {{ $weight }} KG </span></center></th>
                                                        </tr>
                                                        <tr>
                                                            <th width="35%">Item Name</th>
                                                            <th width="10%">Item Qty</th>
                                                            <th width="25%">Addon </th>
                                                            <!-- <th width="20%">Image </th> -->
                                                            <th width="30%">Note </th>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ $value->item_name }}</td>
                                                            <td>{{ $value->pickup_qty }}</td>
                                                            <td>
                                                                @foreach($selected_addons as $add_value)
                                                                    @if(($value->ord_itm_id == $add_value->ord_itm_id) && ($value->item_id == $add_value->item_id))
                                                                        <label class="badge badge-success">{{ $add_value->addon_name }}</label>  
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                            <!-- <td>
                                                                @if($value->item_image)
                                                                    <img src="{{ asset('uploads/orders/'.$value->item_image) }}" alt="users view avatar" class="users-avatar-shadow rounded-circle" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);" height="64" width="64">
                                                                @else
                                                                    {{ 'No Image' }}
                                                                @endif
                                                            </td> -->
                                                            <td>{{ $value->note }}</td>
                                                        </tr>


                                                    <?php }?>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan=2><label class="badge badge-info">{{ "N/A" }} </label></td>
                                                </tr>
                                                
                                            @endif
                                    <tbody>
                                    

                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            @endif

            @if(isset($selected_services))
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Summary</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                        <table class="table dt-responsive">
                                        <thead>
                                            <tr>
                                                <th width="40%">Service Name</th>
                                                <th width="25%">Weight</th>
                                                <th width="25%">Qty</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        @if($selected_services)
                                        <?php $tot_qty = 0 ; $tot_weight=0;?>
                                            <tbody>
                                                @foreach($selected_services as $value)
                                                    <?php 
                                                        $tot_qty +=$value->service_qty; 
                                                        $tot_weight +=$value->weight; 
                                                    ?>
                                                    <tr>
                                                        <th>Total Weight of  {{ $value->service_name }} in Kg</th>
                                                        <td>{{ $value->weight }}</td>
                                                        <td>{{ $value->service_qty }} </td>
                                                        <td> </td>
                                                    </tr>
                                                @endforeach
                                            <tbody>
                                        @endif
                                        
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th>{{ $tot_weight }}</th>
                                                <th>{{$tot_qty}}</th>
                                                <td></td>
                                            </tr>
                                        </tfoot>

                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(isset($histories))
                <div class="card card-custom card-collapsed d-print-none" data-card="true">
                    
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Order History</h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle" style="margin-left: 5px">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                        <table class="table dt-responsive">
                                            <thead>
                                                <tr>
                                                    
                                                    <th width="20%">User</th>
                                                    <th width="20%">Role</th>
                                                    <th width="20%">Status Name</th>
                                                    <th width="20%">Timestamp</th>
                                                    <th width="20%">View Trial</th>
                                                    
                                                </tr>
                                            </thead>
                                            @if(isset($histories))
                                                <tbody>
                                                    @foreach($histories as $value)
                                                    
                                                        <tr>
                                                            <th>{{ $value->user_name }} </th>
                                                            <th>{{ $value->role_name }} </th>
                                                            <th>{{ $value->status_name }} </th>
                                                            <td>{{ $value->created_at }}</td>
                                                            
                                                            <td>
                                                                @if((isset($value->detail)) && ($value->detail != null)  )
                                                                    <a href="/order_verifies/fetch_history/{{ $value->history_id }}" class="btn btn-primary btn-sm"> View</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                <tbody>
                                            @endif
                                    
                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
       </div>
   </div>

   <script>
    // $('.print-window').click(function() {
    //         window.print();
    //     });

		function printDiv(divName){
            // var div = document.getElementById('btns');
            //     div.remove();
            // $(".btns").remove();
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;
          
            // console.log( printContents.children(".btns")) ;
            // console.log(printContents)
			document.body.innerHTML = printContents;
            
			window.print();
            // $(".btns").append();
			document.body.innerHTML = originalContents;

		}
    </script>
@endsection
