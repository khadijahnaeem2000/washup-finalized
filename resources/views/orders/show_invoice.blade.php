@extends('layouts.master')
@section('title','Order PolyBags')
@section('content')



<style>
    @page{
            width:115px;
            /* height: 480px; */
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
                            <a  href="{{ route('orders.index') }}" class="btn btn-primary btn-sm ">
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
                                @if($data)
                               
                                    <table class="pc-email-body" width="900" align="center" bgcolor="#fafafa" border="0" cellpadding="0" cellspacing="0" role="presentation" style="table-layout: fixed;width: 1000px;margin: 0 auto;">
                                        <tbody>
                                            <tr>
                                            <td class="pc-email-body-inner" align="center" valign="top" style="border:1px solid #0000003d;color: #262626;width: 1000px;margin: 0px auto;display: block;padding: 40px 0px 15px 0px;">
                                                <table class="pc-email-container" width="900" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 1000px;margin: 0 auto;">
                                                    <tbody>
                                                        <tr>
                                                        <td align="left" valign="top">
                                                            <!-- BEGIN MODULE: 1 -->
                                                            <table width="90%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                                <tbody>
                                                                    <tr>
                                                                    <td>
                                                                        <table width="90%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                                            <tbody>
                                                                                <tr>
                                                                                <td class="pc-sm-p-24-20-30 pc-xs-p-15-10-20" valign="top" style="">
                                                                                    <table width="90%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                                                        <tr>
                                                                                            <td>
                                                                                            <table width="900" border="0" cellspacing="0" cellpadding="0" role="presentation" >
                                                                                                <tr>
                                                                                                <td align="left" style="padding: 0px 0px 0px 20px;display:block;">
                                                                                                    <table border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                                                                        <tbody >
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;margin-right: 10px;"><img width="20" src='http://app.washup.com.pk/public/mail_assets/images/www.png'></td>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block; font-family: 'Poppins', sans-serif;">
                                                                                                                            <a class="smalltext_fone" style="text-decoration: none;color: #424952;font-size: 13px;font-weight: 700;" target="_blank" href="www.washup.com.pk">www.washup.com.pk</a>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;margin:0px 10px 0px 0px ;"><img width="20" src='http://app.washup.com.pk/public/mail_assets/images/mail.png'></td>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                                                            <a class="smalltext_fone" style="text-decoration: none;color: #424952;font-size: 13px;font-weight: 700;" href="mailto:info@washup.com.pk">info@washup.com.pk</a>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>   
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;margin:0px 10px 0px 0px ;"><img width="20" src='http://app.washup.com.pk/public/mail_assets/images/phone.png'></td><td style="padding: 0px 0px 0px 0px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                                                            <a class="smalltext_fone" style="text-decoration: none;color: #424952;font-size: 13px;font-weight: 700;" href="tel:0317-5286379">0317-5286379</a>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                                <td align="center" style="padding: 0px 0px 0px;">
                                                                                                    <table border="0" cellspacing="0" cellpadding="0" role="presentation" style="margin:0px auto;">
                                                                                                        <tbody >
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0" >
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        <td>
                                                                                                                            <a target="_blank" href="www.washup.com.pk"><img style="display: block;max-width: 190px;width: auto;height:60px; margin: 0px auto;" src='http://app.washup.com.pk/public/mail_assets/images/logo.png'></a>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                                <td align="right" style="padding: 0px 20px 0px 0px;display:block;">
                                                                                                    <table border="0" cellspacing="0" cellpadding="0" role="presentation" style="margin-left: auto;">
                                                                                                        <tbody >
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block; font-family: 'Poppins', sans-serif;">
                                                                                                                            <p style="margin:0px;color: #424952;font-size: 13px;font-weight: 700;">Order: {{ $data->id }}</p>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                                                            <p style="margin:0px;color: #424952;font-size: 13px;font-weight: 700;">Name: {{ $data->customer_name }}</p>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>   
                                                                                                            <td>
                                                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                                                    <tbody>
                                                                                                                    <tr>
                                                                                                                        <td style="padding: 0px 0px 0px 0px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                                                            <p style="margin:0px;color: #424952;font-size: 13px;font-weight: 700;">Number: {{ $data->contact_no }} </p>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr style="display:block;width:100%;">
                                                                                            <td style="display: block;">
                                                                                            <div style="text-align: center;">
                                                                                            <table width="90%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                                                                <tr>
                                                                                                    <td valign="top" style="display: block;padding: 20px 25px 0px; ">
                                                                                                    <p style="font-family: 'Poppins', sans-serif;font-size: 8px;margin-top:0px;text-align: center;font-weight: 700;">Condition of Services: By tendering goods for laundry by Washup the consignee agrees to be bound by Washup's terms & conditions mentioned at the bottom of this page</p> 
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                            </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                </tr>
                                                                                
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div align="center">
                                                            <table align="center" width="900" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                                <tr align="center" style="text-align: center;">
                                                                <td style="padding: 15px 30px 30px;display:block;">
                                                                    <table border="0" cellspacing="0" cellpadding="0" role="presentation" style="margin: 0px auto;">
                                                                    <tbody >
                                                                        <tr align="center">
                                                                            <td>
                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="margin-right:50px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                            <p style="margin:0px;color: #424952;font-size: 13px;font-weight: 700;">Pickup Date: {{ $data->pickup_date }}</p>
                                                                                        </td>                                                      
                                                                                    </tr>
                                                                                </tbody>
                                                                                </table>
                                                                            </td>
                                                                            <td>
                                                                                <table border="0" cellspacing="0" cellpadding="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="margin-right:10px;display: inline-block;font-family: 'Poppins', sans-serif;">
                                                                                            <p style="margin:0px;color: #424952;font-size: 13px;font-weight: 700;">Delivery Date: {{ $data->delivery_date }}</p>
                                                                                        </td>                                                      
                                                                                    </tr>
                                                                                </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="clear: both;"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                    </table>
                                                                <td>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                            @if(isset($record))
                                                                <table align="center" border="0" cellspacing="0" cellpadding="0" width="900" style="margin: 0px auto; width:1000px;">
                                                                    <tr>
                                                                        <td style="padding-bottom: 30px;">
                                                                            <div style="text-align: center;" >
                                                                                <table style="border:1px solid black;border-collapse:collapse;margin: 0px auto;" width="900" >
                                                                                    <?php $mega_ser_tot = 0; $mega_add_tot = 0;  $mega_qty_tot = 0; $item_count = 0;?>
                                                                                    @foreach($record as $key => $value)
                                                                                        <?php $ser_tot =0;  $qty_tot = 0; $mega_qty_tot += ($value->weight); $add_tot = 0; $add_total = 0;?>
                                                                                        <tr>
                                                                                            <th align="center" colspan="7" style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 37%;">{{$value->service_name }}</th>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 37%;">Items</th>
                                                                                            <th style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 10%;" >Qty</th>
                                                                                            <th style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 10%;" >Kg</th>  
                                                                                            <th style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 25%;">Addons</th>
                                                                                            <th style="border:1px solid #0000003d;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 12%;" >Total</th>
                                                                                        </tr>
                                                                                        @foreach($value->items as $item_key => $item_value)
                                                                                        <?php  $qty_tot += ($item_value->pickup_qty); $item_count +=($item_value->pickup_qty);  $add_total = 0; ?>
                                                                                            <tr>
                                                                                                <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;">{{$item_value->item_name}}</td>   
                                                                                                <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;">{{$item_value->pickup_qty}}</td>
                                                                                                <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;" >-</td>
                                                                                                <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;">
                                                                                                    @foreach($item_value->addons as $addon_key => $addon_value)
                                                                                                        <span style="padding-right: 3px;"> {{$addon_value->addon_name }} </span> <br> 
                                                                                                        <?php $add_total += (($addon_value->addon_rate) * ($item_value->pickup_qty));?> 
                                                                                                    @endforeach
                                                                                                    <?php $add_tot +=  $add_total;?>
                                                                                                </td>
                                                                                                <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;">
                                                                                                    @if($value->unit_id ==2 )
                                                                                                        <?php $ser_tot += (($item_value->item_rate) * ($item_value->pickup_qty));  ?>
                                                                                                        <!-- {{$item_value->item_rate}} -->
                                                                                                        {{ ((($item_value->item_rate) * ($item_value->pickup_qty)) + $add_total )  }}
                                                                                                    @elseif($value->unit_id ==3)
                                                                                                        <?php $ser_tot += (($item_value->service_rate) * ($item_value->pickup_qty));  ?>
                                                                                                        <!-- {{$item_value->service_rate}} -->
                                                                                                        {{( (($item_value->service_rate) * ($item_value->pickup_qty)) +  $add_total )}}
                                                                                                    @else
                                                                                                        <?php $ser_tot =$item_value->service_rate;  ?>
                                                                                                        <!-- sr: {{$item_value->service_rate}} -->

                                                                                                    @endif
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        
                                                                                        <tr>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;">Sub-Total</td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;font-weight: 600;" >{{$qty_tot}}</td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;">{{$value->weight }}</td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;" ></td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;">
                                                                                                @if($value->unit_id ==1 )
                                                                                                <?php $tot = ($ser_tot * ($value->weight))?>
                                                                                                @else
                                                                                                <?php $tot = $ser_tot ?>
                                                                                                @endif

                                                                                                <?php 
                                                                                                    echo "Rs.". ($tot + $add_tot);
                                                                                                    $mega_ser_tot +=($tot + $add_tot) ;
                                                                                                ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <!-- <tr>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;">Total</td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;font-weight: 600;" ></td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;"></td>
                                                                                            <td colspan ="2" style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;" >Rs.{{ ($tot + $add_tot) }}</td>
                                                                                            <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;font-weight: 600;"></td>
                                                                                        </tr> -->
                                                                                        @endforeach
                                                                                        
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td  style="padding-bottom: 30px;">   
                                                                        <div style="text-align: center;">
                                                                            <table style="border:1px solid black;border-collapse:collapse;margin: 0px auto;" width="900" >
                                                                                <tr>
                                                                                    <td style="border:1px solid #0000003d; border-right:0px;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 37%;font-weight: 600;">Delivery Charges</td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;width: 10%;" ></td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 10%;" ></td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0; font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 25%;" ></td>
                                                                                    <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 12%;font-weight: 600;">Rs.  {{ $data->delivery_charges }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="border:1px solid #0000003d; border-right:0px;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 37%;font-weight: 600;">GST Charges</td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;width: 10%;" ></td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 10%;" ></td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0; font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 25%;" ></td>
                                                                                    <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 12%;font-weight: 600;">Rs.  {{ $data->vat_charges }}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                        </td>
                                                                    </tr>


                                                                    <tr>
                                                                        <td style="padding-bottom: 30px;">   
                                                                        <div style="text-align: center;">
                                                                            <table style="border:1px solid black;border-collapse:collapse;margin: 0px auto;" width="900" >
                                                                                <tr>
                                                                                    <td style="border:1px solid #0000003d; border-right:0px;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 37%;font-weight: 600;">Grand Total</td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0; font-size: 15px;font-family: 'Poppins', sans-serif;color: #000; padding: 0px 4px;width: 10%;font-weight: 600;" >{{$item_count}} Items</td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0; font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 10%;font-weight: 600;" >{{$mega_qty_tot}} Kg</td>
                                                                                    <td style="border: 1px solid #0000003d;border-color:#0000003d;border-right:0px;border-left: 0; font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 25%;font-weight: 600;" ></td>
                                                                                    <td style="border:1px solid #0000003d;font-size: 15px;font-family: 'Poppins', sans-serif;color: #000;padding: 0px 4px;width: 12%;font-weight: 600;"> Rs. {{ ( ($mega_ser_tot) + ($data->delivery_charges ) + ($data->vat_charges ) ) }}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>           
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="padding-left: 55px;padding-bottom: 10px;">  
                                                                        <a href="http://app.washup.com.pk/public/mail_assets/terms_and_conditions.pdf" target="_blank"> * Click here to view terms and conditions</a>
                                                                        </td>
                                                                    <tr>
                                                                </table>
                                                            @endif
                                                            <!-- END MODULE: 1 -->
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                  
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
