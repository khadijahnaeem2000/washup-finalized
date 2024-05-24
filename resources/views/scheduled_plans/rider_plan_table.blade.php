<div class="col-md-12" style="overflow-x: auto;">
    <table class="table" id="order_table" style="width: 100%;" cellspacing="0">
        <thead>
            <tr>
                <th></th>
                <th>#</th>
                <th>Name</th>
                <th>Contact#</th>
                <th>Action</th>
                <th>Type</th>
                <th>Weight</th>
                <th>Hanger</th>
                <th>TimeSlot</th>
                <th>Address</th>
                <th>Area</th>
                <th>Zone</th>
                <th>Route</th>
                <th>KMS</th>
                <th>Taken_Time</th>
                <th>Done_Time</th>
                <th>Canceled</th>
                <th>Status</th>
                <th>Polybags</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if((isset($orders)) && (($orders->count()>0)))
                <?php
                    $code = array('#63e2ff','#dbe6c3','#f5e0cb','#63ffe5','#f4c4ff','#e8e8e8','#ffc4c4','#b5ffe1','#bdb4db','#b3f2e1','#c2f7ff','#f9b5ff','#fff9b5','#e1ffb5','#b5ffce','#b6b5ff','#ffb5f3');
                ?>
                @foreach($orders as $odr_key => $odr_value)
                    <?php 
                        $index = 0; 
                        if( (isset($odr_value->route))  && (($odr_value->route) != null) ){
                            $index      = (($odr_value->route)-1);
                            if(array_key_exists($index, $code)){
                                $tr_color   = $code[$index];
                            }else{
                                $tr_color   = $code[0];
                            }
                        }else{
                            $tr_color   =  "";
                        }
                    ?>
                    <tr id ="{{$odr_value->id}}" style="background-color: {{$tr_color}}"> 
                        <td>
                            <div class="checkbox-inline"> 
                                <label class="checkbox checkbox-success">
                                    <input type="checkbox" name="ride_id[{{$odr_value->id}}]" id ="ride_id[{{$odr_value->id}}]"  />
                                    <span></span> 
                                </label>
                            </div>
                        </td>
                        <td>
                            {{ Form::hidden('order_id['.$odr_value->id.']', $odr_value->order_id, array('readonly'=>'true')) }}
                            {{$odr_value->order_id}} 
                        </th>

                        <td>
                            @if(isset($odr_value->cus_name))
                                {{$odr_value->cus_name}}
                            @endif
                        </td>

                        <td>
                            @if(isset($odr_value->cus_contact))
                                {{$odr_value->cus_contact}}
                            @endif
                        </td>
                        
                        <td>
                            @if((isset($odr_value->status_name)) && (($odr_value->status_name) == 'Pickup'))
                                <span class="tGreen">{{$odr_value->status_name}}</span>
                            @else
                                <span class="tRed">{{$odr_value->status_name}}</span>

                            @endif
                        </td>
                        <td>
                            @if(!(isset($odr_value->ref_order_id)) &&( ($odr_value->ref_order_id) == null))
                                <span class="tBlue">REG</span>
                            @else
                                <span class="tRed"> HFQ</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($odr_value->weight) && ($odr_value->weight>0))
                                <span class="tBlue">{{round(($odr_value->weight),2)}}  Kg</span>
                            @endif
                        </td>
                        <td>
                            {{ Form::hidden('hanger['.$odr_value->id.']', $odr_value->hanger, array('readonly'=>'true')) }}
                            @if(isset($odr_value->hanger) && (($odr_value->hanger) == 1))
                                <span class="tBlue"> Y</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($odr_value->timeslot_name))
                                {{$odr_value->timeslot_name}}
                            @endif
                        </td>
                        <td>
                            @if(isset($odr_value->cus_address))
                                
                                <span class="btn btn-secondary btn-sm"  data-toggle="tooltip" data-placement="top" title="{{$odr_value->cus_address}}">
                                    <i class="fas fa-home" ></i></a>
                                </span>
                            @endif
                        </td>
                       <td>
                            @if(isset($odr_value->area_name))
                                {{$odr_value->area_name}}
                            @endif
                        </td>
                        <td>
                            @if(isset($odr_value->zone_name))
                                {{$odr_value->zone_name}}
                            @endif
                        </td> 
                        <td>
                            @if(isset($odr_value->route))
                                {{$odr_value->route}}
                            @endif
                        </td>
                        <td>
                            @if( (isset($odr_value->req_dist)) && (($odr_value->req_dist)!=null) )
                                {{$odr_value->req_dist}} KMs
                            @endif
                        </td>
                        <td>
                            @if( (isset($odr_value->travel_time)) && (($odr_value->travel_time)!=null) )
                                {{$odr_value->travel_time}}
                            @endif
                        </td>
                        <td>
                            @if( (isset($odr_value->time_at_loc)) && (($odr_value->time_at_loc)!=null) )
                                {{$odr_value->time_at_loc}}
                            @endif
                        </td>
                        <td>
                            @if( (isset($odr_value->is_canceled)) && (($odr_value->is_canceled)!=null) )
                                <span class="tRed"> Canceled</span>
                            @endif
                        </td>
                        <td>
                            {{ Form::hidden('complete['.$odr_value->id.']', $odr_value->complete, array('readonly'=>'true')) }}
                            {{ Form::hidden('seq['.$odr_value->id.']', $odr_value->seq, array('readonly'=>'true','class'=>'seq_cls')) }}
                            @if((isset($odr_value->complete)) && ($odr_value->complete) == 0)
                                <i class="fas fa-times tRed"></i>
                            @else
                                <i class="fas fa-check tBlue"></i>
                            @endif
                        </td>
                        <td>
                            @if((isset($odr_value->gen_bags))  && (($odr_value->gen_bags) !=0) )
                                {{$odr_value->scan_bags}} / {{$odr_value->gen_bags}} 
                            @endif
                        </td>
                        <td>
                            @if((isset($odr_value->complete)) && ($odr_value->complete) == 0)
                                <a href="javascript:void(0)"  data-id="{{$odr_value->id}}" class=" route_cls">
                                    <i class="icon-2x text-danger flaticon2-trash"></i>
                                </a>
                            @endif
                        
                        </td>
                        
                    </tr>
                @endforeach
            @else
                <tr>
                <td colspan="16">"<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2></td>
                </td>
            @endif
        </tbody>
    </table>
</div> 
<script type="text/javascript">
    var c = '';
  $('tbody').sortable({
        // stop: function(e,ui) {
        //     $('tbody tr').each(function(index, value) {
        //         // $(this).attr("data-sort", index);/
        //         $(this).find('.seq_cls').val((index+1));
        //     });
            
        // },
        revert: true,
        update: function(e,ui) {
            $('tbody tr').each(function(index, value) {
                $(this).find('.seq_cls').val((index+1));
            });
            var cus_url = "{{ route('scheduled_plans.index') }}" +'/update_order_seq/';
            var form_id = '#rider_plan_form';
            $.ajax({
                data: $(form_id).serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success){
                        toastr.success(data.success);
                    }else{
                        var txt = '';
                        var count = 0 ;
                        $.each(data.error, function() {
                            txt +=data.error[count++];
                            txt +='<br>';
                        });
                        toastr.error(txt);
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        },
  });
</script>


<script>
    $('.route_cls').click(function (e) {
        e.preventDefault();
        var route_id = $(this).attr("data-id");
        var cus_url = "{{ route('scheduled_plans.index') }}" +'/cancel_order/';
        var token  = $("input[name='_token']").val();
        // re_draw_all();
        $.ajax({
            url: cus_url,
            method: 'POST',
            data: {route_id:route_id, _token:token},
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    re_draw_all();
                    toastr.success(data.success);
                }else{
                    var txt = '';
                    var count = 0 ;
                    $.each(data.error, function() {
                        txt +=data.error[count++];
                        txt +='<br>';
                    });
                    toastr.error(txt);
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

        
    });
</script>
<!-- END::fetching customer detail by contact no -->
