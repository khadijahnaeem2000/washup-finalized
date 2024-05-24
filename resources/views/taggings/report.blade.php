<?php if(isset($rec)) {?> 
   <?php foreach($rec as $key =>$value){ ?><tr><td>
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
                @if(isset($value->customer_no))
                    {{$value->customer_no}}
                @endif
            </td>
            <td>
                @if(isset($value->wash_name))
                    {{$value->wash_name}}
                    
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
            </td>
            <td>
                @if(isset($value->customer_type_name))
                    @if($value->customer_type_name != 'Retail')
                        {{ substr($value->customer_type_name,0, 4) }}
                    @else
                        {{$value->customer_type_name}}
                    @endif
                @endif
            </td>
            <td>
                @if((isset($value->pickup_weight)) && (($value->pickup_weight)!=0) )
                    {{round($value->pickup_weight,2)}}
                @endif
            </td>
            <td>
                @if((isset($value->pickup_qty)) && (($value->pickup_qty)!=0) )
                    {{$value->pickup_qty}}
                @endif
            </td>
            <td>
                @if((isset($value->tot)) && (($value->tot)!=0) )
                    {{round($value->tot,2)}}
                @endif
            </td>
            <td>
                @if( (isset($value->delivery)) && (($value->delivery)==1) && (isset($value->tot)) && (($value->tot)!=0)) 
                    <!-- <span ><i class="fas fa-check"></i></span> -->
                    <span style="font-weight: bold;color: green">Y </span>
                @endif
            </td>
            <td>
                @if((isset($value->hanger)) && (($value->hanger)!=0)  && (($value->hanger)==1))
                    <!-- <span ><i class="fas fa-check"></i></span> -->
                    <span style="font-weight: bold;color: green">Y </span>
                @endif
            </td>
            <td>
                @if((isset($value->order_note)) && (($value->order_note)!=null) )
                    <!-- <span ><i class="fas fa-check"></i></span> -->
                    <span style="font-weight: bold;color: green">Y </span>
                @endif
            </td>
            <td>
                @if((isset($value->tagger)) && (($value->tagger)!='') )
                    {{$value->tagger}}
                @endif 
                <br>
             

            </td>
            <td>
                <a class="btn btn-secondary btn-sm" target="_blank" href="taggings/{{$value->id}}">
                    <i class="fa fa-eye"></i>
                </a>
            </td></tr><?php } }?>



