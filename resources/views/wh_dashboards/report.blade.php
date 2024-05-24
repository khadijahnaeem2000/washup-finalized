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
        
                @if((isset($value->addons)) && (($value->addons)!=0)  && (($value->addons)==1))
                    <span style="color: red; font-weight:bold">Y</span>
                @endif
            </td>
            <td>
                @if((isset($value->order_note)) && (($value->order_note)!=null) )
                <span style="color: red; font-weight:bold" >Y</span>
                @endif
            </td>
            <td>
                <a class="btn btn-secondary btn-sm" target="_blank" href="wh_dashboards/{{$value->id}}">
                    <i class="fa fa-eye"></i>
                </a>
            </td></tr><?php } }?>



