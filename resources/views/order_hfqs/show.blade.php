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
                            <a  href="{{ route('order_inspects.index') }}" class="btn btn-primary btn-sm ">
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
                                <label>Status  :</label>
                                <h4>{{ $data->status_name }}</h4>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Pickup Date:</label>
                                <h4> {{$data->pickup_date}}</h4>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Delivery Date:</label>
                                <h4> {{$data->delivery_date}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

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
                                        <?php $id = $selected_items[0]->service_id;?>

                                            <tr>
                                                <th colspan=8><h4>Service: {{ $selected_items[0]->service_name }}</h4></th>
                                            </tr>
                                            <tr>
                                                <th width="20%">Name</th>
                                                <th width="10%">Pickup Qty</th>
                                                <th width="10%">Scan Qty</th>
                                                <th width="10%">BT Qty</th>
                                                <th width="10%">NR Qty</th>
                                                <th width="10%">HFQ Qty</th>
                                                <th width="10%">Addon </th>
                                                <th width="20%">Note </th>
                                            </tr>
                                            

                                            @foreach($selected_items as $value)
                                                <?php if ($id == $value->service_id){ ?>
                                                    <tr>
                                                        <td>{{ $value->item_name }}</td>
                                                        <td>{{ $value->pickup_qty }}</td>
                                                        <td>{{ $value->scan_qty }}</td>
                                                        <td>{{ $value->bt_qty }}</td>
                                                        <td>{{ $value->nr_qty }}</td>
                                                        <td>{{ $value->hfq_qty }}</td>
                                                        
                                                        <td>
                                                            @foreach($selected_addons as $add_value)
                                                                @if(($add_value->service_id == $id) && ($add_value->item_id ==$value->item_id ))
                                                                    {{ $add_value->addon_name }}, 
                                                                @endif
                                                            @endforeach
                                                        </td>

                                                        
                                                        <td>{{ $value->note }}</td>
                                                    </tr>
                                                <?php }else{
                                                    $id = $value->service_id;
                                                    ?>
                                                    <tr>
                                                        <th  colspan=8> <h4> Service: {{ $value->service_name }}</h4></th>
                                                    </tr>
                                                    <tr>
                                                        <th width="20%">Name</th>
                                                        <th width="10%">Pickup Qty</th>
                                                        <th width="10%">Scan Qty</th>
                                                        <th width="10%">BT Qty</th>
                                                        <th width="10%">NR Qty</th>
                                                        <th width="10%">HFQ Qty</th>
                                                        <th width="10%">Addon </th>
                                                        <th width="20%">Note </th>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $value->item_name }}</td>
                                                        <td>{{ $value->pickup_qty }}</td>
                                                        <td>{{ $value->scan_qty }}</td>
                                                        <td>{{ $value->bt_qty }}</td>
                                                        <td>{{ $value->nr_qty }}</td>
                                                        <td>{{ $value->hfq_qty }}</td>
                                                        <td>
                                                            @foreach($selected_addons as $add_value)
                                                                @if(($add_value->service_id == $id) && ($add_value->item_id ==$value->item_id ))
                                                                    {{ $add_value->addon_name }},
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        
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
                                        <th width="50%">Service Name</th>
                                        <th width="10%">Weight</th>
                                        <th width="10%">Pickup Qty</th>
                                        <th width="10%">Delivery Qty</th>
                                        <th width="10%">HFQ Qty</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                @if($details)
                              
                                <?php $tot_pickup = 0 ; $tot_weight=0; $tot_delivery = 0; $tot_hfq = 0;  $delivery_qty = 0 ;?>
                                    <tbody>
                                        @foreach($details as $value)
                                            <?php 
                                                $delivery_qty    = $value['scan_qty'] + $value['bt_qty'] + $value['nr_qty'];
                                                $tot_weight     += $value['service_weight'];
                                                $tot_pickup     += $value['pickup_qty']; 
                                                $tot_hfq        += $value['hfq_qty']; 
                                                $tot_delivery   += $delivery_qty ; 
                                            ?>
                                            <tr>
                                                <th>Total Weight of  {{ $value['service_name'] }} in Kg</th>
                                                <td> {{ $value['service_weight'] }}</td>
                                                <td> {{ $value['pickup_qty'] }} </td>
                                                <td> {{ $delivery_qty }}  </td>
                                                <td> {{ $value['hfq_qty'] }}</td>
                                                <td> </td>
                                            </tr>
                                        @endforeach
                                    <tbody>
                                @endif
                                  
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th>{{ $tot_weight }}</th>
                                        <th>{{ $tot_pickup}}</th>
                                        <th>{{ $tot_delivery}}</th>
                                        <th>{{ $tot_hfq}}</th>
                                        <td></td>
                                    </tr>
                                </tfoot>

                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
           </div>

           <div class="card card-custom gutter-b example example-compact d-print-none">
                
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Order History</h3>
                   </div>
               </div>

               <div class="card-body">
                   <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <thead>
                                        <tr>
                                            <th width="70%">Status Name</th>
                                            <th width="30%">Timestamp</th>
                                            
                                        </tr>
                                    </thead>
                                    @if($histories)
                                        <tbody>
                                            @foreach($histories as $value)
                                            
                                                <tr>
                                                    <th>{{ $value->status_name }} </th>
                                                    <td>{{ $value->created_at }}</td>
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
