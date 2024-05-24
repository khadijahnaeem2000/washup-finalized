<?php if(isset($rec)) {?> 
   <?php foreach($rec as $key =>$value){ ?><tr><td style="width:10%">
                @if(isset($value->id)) 
                    {{$value->id}}
                   
                @endif
            </td>
            <td>
                @if(isset($value->customer_name))
                    {{$value->customer_name}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->wash_name))
                    {{$value->wash_name}}
                    
                @endif
            </td>
            <td>
                @if((isset($value->polybags_printed)) && ($value->polybags_printed == 1))
                    <span style="font-weight: bold;color: green">
                        @if((isset($value->ref_order_id)))
                            HFQ(Y)
                        @else
                            REG(Y)
                        @endif

                    </span>
                @else
                    <span style="font-weight: bold;color: red">
                        @if((isset($value->ref_order_id)))
                                HFQ(N)
                            @else
                                REG(N)
                            @endif
                        @endif
                    </span>
            </td>
            <td>
                @if((isset($value->pickup_tags)) && (($value->pickup_tags)!=0) )
                    {{$value->pickup_tags}}
                @endif
            </td>
            <td>
                @if((isset($value->scan_tags)) && (($value->scan_tags)!=0) )
                    {{$value->scan_tags}}
                @endif
            </td>
          
            <td>
                @if((isset($value->nr_tags)) && (($value->nr_tags)!=0) )
                    {{$value->nr_tags}}
                @endif
            </td>
            <td>
                @if((isset($value->bt_tags)) && (($value->bt_tags)!=0) )
                    {{$value->bt_tags}}
                @endif
            </td>
            <td>
                @if((isset($value->packed_tags)) && (($value->packed_tags)!=0) )
                    {{$value->packed_tags}}
                @endif
            </td>
             <td>
                <span style="font-weight: bold;color: red">
                    @if((isset($value->hfq_tags)) && (($value->hfq_tags)!=0) )
                        {{$value->hfq_tags}}
                    @endif
                 </span>
            </td>
            <td>
                @if((isset($value->pickup_weight)) && (($value->pickup_weight)!=0) )
                    {{round($value->pickup_weight)}}
                @endif
            </td>
            <td>
                @if((isset($value->packed_weight)) && (($value->packed_weight)!=0) && (($value->packed_weight)!= null) )
                    {{round($value->packed_weight)}}
                @endif
            </td>
            <td>
                @if((isset($value->polybags)) && (($value->polybags)!=0) )
                    {{$value->polybags}}
                @endif
            </td>
            <td>
                @if((isset($value->delivery_rider)) )
                    {{$value->delivery_rider}}
                @endif
            </td>
            <td>
                @if((isset($value->route)) && (($value->route)!= null ) )
                    Route: {{$value->route}}
                @endif
            </td>

            
            <td>
                @if((isset($value->packer)) && (($value->packer)!= '0') )
                    {{$value->packer}}
                @endif
            </td>
            <td>
                @if((isset($value->pack_dt)) && (($value->pack_dt)!=0) )
                    {{$value->pack_dt}}
                @endif
            </td>
            <td>
                @if((isset($value->pack_tm)) && (($value->pack_tm)!=0) )
                    {{$value->pack_tm}}
                @endif
            </td>
            <td>
                @if(isset($value->pickup_date))
                    {{ $value->pickup_date }}
                @endif
            </td>
            <td>
                @if(isset($value->delivery_date))
                    {{$value->delivery_date}}
                @endif
            </td></tr><?php } }?>



