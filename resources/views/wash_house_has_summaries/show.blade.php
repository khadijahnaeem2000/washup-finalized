@extends('layouts.master')
@section('title','Summary')
@section('content')
<style>
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }

       
        /* table { page-break-after:always } */
        /* tr    { page-break-inside:avoid; page-break-after:auto } */
    }
    table{
        border:1px solid black;
        /* page-break-after:always; */
       
    }
    .tbl_br{
            page-break-after:always;
        }
    .myStyle{
        font-size:14px;
        font-weight:bold;
    }
   
    #cssTable td 
    {
    	/*color:black;*/
        text-align: center; 
        vertical-align: middle;
    }
    body{
    	color: black;
    }
    
</style>
    <?php 
        function createAcronym($string) {
            $output = null;
            $token  = strtok($string, ' ');
            while ($token !== false) {
                $output .= $token[0];
                $token = strtok(' ');
            }
            return $output;
        }
    ?>
    <div class="row" id="main">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header py-3 d-print-none">
                    <div class="card-title">
                        <h3 class="card-label">Show @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="btn-group btn-group">
                            <a  href="{{ route('wash_house_summaries.index') }}" class="btn btn-primary btn-sm ">
                            <i class="fas fa-arrow-left"></i></a>
                            <button    class="btn btn-info btn-sm "  onclick="printDiv('main')">
                            <i class="fa fa-print"></i></button>
                        </div>
                    </div>
                </div>
                @if(!(empty($regular_orders)))
                    <div class="card-body">
                    
                        <div style="page-break-before:always">
                            <div class="row" style=" margin-bottom:10px;">
                                <div class="col-12">
                                    <h2 style="color:#000"><center>Regular Orders  (Summary)</center></h2> 
                                </div>
                            </div>
                            <div class="row" style=" margin-bottom:10px;">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <span class="myStyle" style="text-align:left;display: block; color:#000">Pickup Date: {{$pickup_date}}</span> 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="myStyle" style="text-align:center;display: block; color:#000"> Delivery Date : {{$delivery_date}}</span> 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="myStyle"  style="text-align:right;display: block; color:#000">Wash house Name : {{$wash_house_name}}</span> 
                                </div>
                            </div>

                         

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered dt-responsive" id="cssTable" >
                                            <thead>
                                            
                                                <tr>
                                                    <td width="10%" style="color:#000">Order#</td>
                                                    <td width="10%" style="color:#000">Comments</td>
                                                    @foreach($services as $key=>$value)
                                                        <td width="5%" style="color:#000">{{createAcronym($value, false)}}</td>
                                                    @endforeach
                                                    <td width="10%" style="color:#000">Total</td>
                                                    <td width="15%" style="color:#000">Delivered Pcs</td>
                                                    <td width="15%" style="color:#000">Broken Tags</td>
                                                    <td width="10%" style="color:#000">HFQ</td>
                                                </tr>

                                            <thead>
                                            <tbody>
                                                <?php 
                                                    $tot_pieces = 0; $service = array();
                                                    foreach($services as $s_key=>$s_value){
                                                        $arr_service[$s_key] = 0;
                                                    }
                                                ?>
                                                @foreach($regular_orders as $key =>$value)
                                                    <?php 
                                                        $pieces = 0;
                                                        foreach($services as $s_key => $s_value){
                                                            if(!(isset($value[$s_key]))){
                                                                $value[$s_key] = 0;
                                                            }
                                                            $pieces+= $value[$s_key];
                                                        }
                                                
                                                        foreach($services as $s_key=>$s_value){
                                                            $arr_service[$s_key] += $value[$s_key];
                                                        }
                                                        $tot_pieces += $pieces;

                                                        if(isset($value['note'])){
                                                            $cls ="table-success";
                                                            $note = "Y";
                                                        }else{
                                                            $cls ="";
                                                            $note = "";
                                                        } 
                                                    ?>
                                                    <tr >
                                                        <td style="color:#000">{{$value['order_id']}} </td>
                                                        <td style="color:#000"> {{$note}}  </td>
                                                        @foreach($services as $s_key => $s_value)
                                                            <td style="color:#000">{{$value[$s_key]}} </td>
                                                        @endforeach
                                                        <th style="text-align:center;color:black">{{$pieces }} </th>
                                                        <td style="color:#000"> </td>
                                                        <td style="color:#000"> </td>
                                                        <td style="color:#000"> </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    @foreach($services as $s_key => $s_value)
                                                        <td style="color:#000">{{$arr_service[$s_key]}} </td>
                                                    @endforeach
                                                    <th style="text-align:center; color:#000">{{$tot_pieces}}</th>
                                                    <td></td>
                                                    <td></td>
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

                @if(!(empty($urgent_orders)))
                    <div class="card-body">
                    
                        <div style="page-break-before:always">
                            <div class="row" style=" margin-bottom:10px;">
                                <div class="col-12">
                                    <H2 style="color:#000"><center>Urgent Orders  (Summary)</center></H2> 
                                </div>
                            </div>
                            <div class="row" style=" margin-bottom:10px;">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <span class="myStyle" style="text-align:left;display: block; color:#000">Pickup Date: {{$pickup_date}}</span> 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="myStyle" style="text-align:center;display: block; color:#000"> Delivery Date : </span> 
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="myStyle"  style="text-align:right;display: block; color:#000">Wash house Name : {{$wash_house_name}}</span> 
                                </div>
                            </div>

                            

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered dt-responsive" id="cssTable" >
                                            <thead>
                                            
                                                <tr>
                                                    <td width="10%" style="color:#000">Order#</td>
                                                    <td width="10%" style="color:#000">Comments</td>
                                                    @foreach($services as $key=>$value)
                                                        <td width="5%" style="color:#000">{{createAcronym($value, false)}}</td>
                                                    @endforeach
                                                    <td width="10%" style="color:#000">Total</td>
                                                    <td width="15%" style="color:#000">Delivered Pcs</td>
                                                    <td width="15%" style="color:#000">Broken Tags</td>
                                                    <td width="10%" style="color:#000">HFQ</td>
                                                </tr>

                                            <thead>
                                            <tbody>
                                                <?php 
                                                    $tot_pieces = 0; $service = array();
                                                    foreach($services as $s_key=>$s_value){
                                                        $arr_service[$s_key] = 0;
                                                    }
                                                ?>
                                                @foreach($urgent_orders as $key =>$value)
                                                    <?php 
                                                        $pieces = 0;
                                                        foreach($services as $s_key => $s_value){
                                                            if(!(isset($value[$s_key]))){
                                                                $value[$s_key] = 0;
                                                            }
                                                            $pieces+= $value[$s_key];
                                                        }
                                                
                                                        foreach($services as $s_key=>$s_value){
                                                            $arr_service[$s_key] += $value[$s_key];
                                                        }
                                                        $tot_pieces += $pieces;

                                                        if(isset($value['note'])){
                                                            $cls ="table-success";
                                                            $note = "Y";
                                                        }else{
                                                            $cls ="";
                                                            $note = "";
                                                        } 
                                                    ?>
                                                    <tr >
                                                        <td style="color:#000">{{$value['order_id']}} </td>
                                                        <td style="color:#000"> {{$note}}  </td>
                                                        @foreach($services as $s_key => $s_value)
                                                            <td style="color:#000">{{$value[$s_key]}} </td>
                                                        @endforeach
                                                        <th style="text-align:center;color:#000">{{$pieces }} </th>
                                                        <td> </td>
                                                        <td> </td>
                                                        <td> </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    @foreach($services as $s_key => $s_value)
                                                        <td style="color:#000">{{$arr_service[$s_key]}} </td>
                                                    @endforeach
                                                    <th style="text-align:center;color:#000">{{$tot_pieces}}</th>
                                                    <td></td>
                                                    <td></td>
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
                <div class="card-body">
                    <div class="row" style=" margin-bottom:10px;">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <span class="myStyle" style="color:#000">Tagger Signature: <p style="border: 2px solid black;"></p></span> 
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <span class="myStyle" style="color:#000">WH Signature: <p style="border: 2px solid black;"></p></span> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
		function printDiv(divName){
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
		}
    </script>
    
@endsection
