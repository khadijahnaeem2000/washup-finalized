@extends('layouts.master')
@section('title','Order PolyBags')
@section('content')

<style>
    @page{
            width:115px;
            height: 480px;
    }
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
       
        th,td{
            
        }
        th{
            font-size: 20px;
        }
    }

    th,td{
        padding-left:3px;
        font-weight: bold;
        color: black;
    }
    th{ font-size: 20px;}
    .tdCls{
        font-size: 20px;
        padding-left: 10px;
    }
    /*table { page-break-after:always }*/
    
    
   
    
</style>

  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3 d-print-none">
                    <div class="card-title">
                        <h3 class="card-label">@yield('title')</h3>
                    </div>
                    <div class="card-toolbar " >
                        <div class="btn-group btn-group">
                            <a  href="{{ route('order_inspects.index') }}" class="btn btn-primary btn-sm ">
                            <i class="fas fa-arrow-left"></i></a>
                            <button    class="btn btn-info btn-sm "  onclick="printDiv('main')">
                            <i class="fa fa-print"></i></button>
                        </div>
                    </div>
                </div>
              

                <div class="card-body" id="main"  style = "padding: 0px;">
                   <div class="row"  >
                       <div class="col-12 col-md-12" >
                           <!--<div class="table-responsive">-->
                                @if($bags)
                                    @foreach($bags as $key =>$value)
                                        <table class="" style="border: 1px solid black;margin-top: 5px;margin-left:3px;width:430px;height:450px;max-height: 450px;page-break-after:always;color:black;display: table;padding: 10px;" >
                                            <thead style="width:100%;">
                                                <tr>
                                                    <td colspan="2" style="text-align:center;">
                                                        <img src="{{ asset('uploads/bag_logo.png') }}" alt="users view avatar" style="height: 80px; width: auto;">
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody style="width:100%;">
                                                <tr style="padding-top:10;">
                                                    <td width="50%"  style="font-size: 20px;"><b>Client</b></td>
                                                    <?php $name = explode(" ",$value->name);?>
                                                    <td width="50%" style="font-size: 20px;">{{ substr($name[0], 0, 6) }} </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%"  style="font-size: 20px;"><b>Order#</b></td>
                                                    <td width="50%" class="tdCls"  style="font-size: 20px;">{{$value->order_id}}</th>
                                                    
                                                </tr>

                                                <tr>
                                                    <td width="50%"  style="font-size: 20px;"><b>Packed</b></td>
                                                    <td width="50%" class="tdCls"  style="font-size: 20px;"><u>{{$packed_qty}}</u> Items</th>
                                                </tr>
                                                
                                                <tr>
                                                    <td width="50%"  style="font-size: 20px;"><b>Held for Quality</b></td>
                                                    <td width="50%" class="tdCls"  style="font-size: 20px;"><u>{{$hfq_qty}}</u> Items </th>
                                                </tr>
                                                <tr>
                                                    <td width="50%"  style="font-size: 20px;"><b>Total </b></td>
                                                    <td width="50%" class="tdCls"  style="font-size: 20px;"><u>{{($hfq_qty+$packed_qty)}}</u> Items </th>
                                                </tr>
                                                <tr>
                                                    <td width="50%"  style="font-size: 20px;"><b>Polybags </b></td>
                                                    <td width="50%" class="tdCls"  style="font-size: 20px;"> <?php echo ++$key." of ". $tot_bags;?> </th>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan=2  style="text-align: center; ">
                                                        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($value->bag_code, 'QRCODE')}}" alt="barcode" class="barcode_image" style="height: 75px;max-width: 250px;margin: 15px -130px 15px;color:black;"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        <span style="text-align:center; font-size:12px; font-weight:bold">
                                                        
                                                            www.washup.com.pk
                                                        </span>
                                                    </td>
                                                </tr>
                                            <tbody>
                                        </table>
                                    @endforeach
                                @endif
                              
                           <!--</div>-->
                       </div>
                   </div>
                </div>
            </div>
       </div>
   </div>

    <script>
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
