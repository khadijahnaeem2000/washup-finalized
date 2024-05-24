<?php if(isset($rec)) { ?>
   <?php foreach($rec as $key =>$value){  ?><tr><td>
            @if(isset($value->srno))
                    {{$value->srno}}
                @endif
            </td>
            <td style="font-weight:bold">
                @if(isset($value->customer_name))
                    {{$value->customer_name}}
                @endif
            </td>
            <td>
                @if(isset($value->contact_no))
                    {{$value->contact_no}}
                @endif
            </td>
            <td>
                @if(isset($value->in_amount))
                    {{$value->in_amount}}
                @endif
            </td>
            <td>
                @if(isset($value->out_amount))
                    {{$value->out_amount}}
                @endif
            </td>
            <td>
                @if(isset($value->reason_name))
                    {{$value->reason_name}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->detail))
                    {{$value->detail}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->created_date))
                    {{$value->created_date}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->created_month))
                    {{$value->created_month}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->created_time))
                    {{$value->created_time}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->id))
                    
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="customer_wallets/{{$value->id}}">
                            <i class="fa fa-eye"></i>
                        </a>
                        @if( (isset($value->out_amount) ) &&(($value->out_amount)==0)&& (($value->ride_id) == null) && (($value->rider_id) == null)  )
                        <a class="btn btn-secondary btn-sm" href="customer_wallets/{{$value->id}}/edit" id="{{$value->id}}">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        @endif
                    </div>
                    
                @endif
            </td></tr><?php }}?>



