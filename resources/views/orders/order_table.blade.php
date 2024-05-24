<?php if(isset($rec)) { $i = 0; ?>
   <?php foreach($rec as $key =>$value){ $i++; $color= ""; if( (isset($value->has_hfq)) && ($value->has_hfq == 'Yes') )  { $color = "yellow"; }  ?><tr style="background:{{$color}}"><td style="width:5%">
                {{$i}}
            </td>
            <td style="font-weight:bold" >
                @if(isset($value->id))
                    {{$value->id}}
                @endif
            </td>
            <td>
                @if(isset($value->ref_order_id))
                    {{$value->ref_order_id}}
                @endif
            </td>
            <td>
                @if(isset($value->has_hfq))
                    {{$value->has_hfq}}
                @endif
            </td>
            <td  style="width:15%">
                @if(isset($value->name))
                    {{$value->name}}
                @endif
            </td>
            <td style="width:15%">
                @if(isset($value->contact_no))
                    {{$value->contact_no}}
                @endif
            </td>
            <td>
                @if(isset($value->pickup_date))
                    {{$value->pickup_date}}
                @endif
            </td>
            <td>
                @if(isset($value->delivery_date))
                    {{$value->delivery_date}}
                @endif
            </td>
            <td style="width:15%"> 
                @if(isset($value->status_name))
                    {{$value->status_name}}
                @endif
            </td>

            <td>

                <div class="btn-group btn-group">
                    <a class="btn btn-secondary btn-sm" href="orders/{{$value->id}}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a  href="/orders/send_invoice/{{$value->id}}" class="btn btn-primary btn-sm">
                        <i class="fa fa-file-invoice"></i>
                    </a>
                </div>
            </td>
        </tr><?php 
        } 
    }
?>




