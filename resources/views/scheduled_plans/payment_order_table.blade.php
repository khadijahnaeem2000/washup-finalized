
<div class="col-md-12">
    <table class="table" id="">
        <thead>
            <tr>
                <th width="2%"></th>
                <th width="3%">#</th>
                <th width="15%">Customers</th>
                <th width="10%">Contact#</th>
                <th width="15%">Address</th>
                <th width="10%">Bill</th>
                <th width="10%">Date</th>
                <th width="10%">TimeSlot</th>
                <th width="10%">Status</th>
                <th width="15%">Rider</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if((isset($orders)) && (!($orders->isEmpty()))) { $i = 0;
                    foreach ($orders as $key => $value) { $i++;?>
                        <tr>
                            <td width="2%">
                                @if( (isset($value->status_name)) && (($value->status_name) != 'Delivered') &&  ($value->status_name) != 'Cancelled')
                                    <div class="checkbox-inline"> 
                                        <label class="checkbox checkbox-success">
                                            <input type="checkbox" name="ride_id[{{$value->id}}]" id ="ride_id[{{$value->id}}]" class="payment_id"  />
                                            <span></span> 
                                        </label>
                                    </div>
                                @endif
                            </td>
                            <td width="3%">{{$i}}</td>
                            <td width="15%">{{$value->customer_name}}</td>
                            <td width="10%">{{$value->contact_no}}</td>
                            <td width="15%">{{$value->address}}</td>
                            <td width="10%">{{$value->bill}}</td>
                            <td width="10%">{{ date_format(date_create($value->created_at),"d/m/Y") }}</td>
                            <td width="10%">{{$value->time_slot_name}}</td>
                            <td width="10%">
                                @if((isset($value->status_name)) && (($value->status_name) == 'Rescheduled'))
                                    <span class="tBlue">Scheduled</span>
                                @elseif((isset($value->status_name)) && (($value->status_name) == 'Cancelled'))
                                    <span class="tRed">{{$value->status_name}}</span>
                                @else
                                    <span class="tGreen">{{$value->status_name}}</span>
                                @endif
                            </td>
                            <td width="15%">
                                @if((isset($value->status_name)) && (($value->status_name) == 'Delivered'))
                                    <span class="tGreen">{{$value->rider_name}}</span>
                                @elseif((isset($value->status_name)) && (($value->status_name) == 'Cancelled'))
                                    @if((isset($value->rider_name)) )
                                        <span class="tRed">{{$value->rider_name}}</span>
                                    @endif
                                @else
                                    {!! Form::select('rider_id['.$value->id.']',$riders, $value->rider_id, array('class' => 'form-control','required'=>'true','id'=>'rider_id['.$value->id.']')) !!}
                                @endif
                            </td>
                        </tr><?php 
                    }
                }else{?>
                    <tr>
                        <td colspan="10">"<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2></td>
                    </tr> <?php
                }
            ?> 
        </tbody>
    </table>
</div> 