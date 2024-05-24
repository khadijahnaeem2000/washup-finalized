<div class="col-md-12 offset-md-0">
        <table class="table" id="order_table">
            <thead>
                <tr>
                    <th width="2%"></th>
                    <th width="3%">Order#</th>
                    <th width="4%">Customer</th>
                    <th width="10%">Action</th>
                    <th width="5%">Type</th>
                    <th width="5%">Weight</th>
                    <th width="5%">Hanger</th>
                    <th width="10%">TimeSlot</th>
                    <th width="20%">Address</th>
                    <th width="7%">Area</th>
                    <th width="8%">Zone</th>
                    <th width="15%">Rider</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if((isset($orders)) && (($orders))) {
                        // $pre_rider = $orders[0]->rider_id;
                        // $seq = 0;
               
                  
                        // foreach($orders as $odr_key => $odr_value){
                        //     $rec[$odr_value->rider_id]['route']           = 1;
                        //     $rec[$odr_value->rider_id]['drop_size']       = 0;
                        //     $rec[$odr_value->rider_id]['drop_weight']     = 0;
                        //     $rec[$odr_value->rider_id]['pick']            = 0;

                        //     $rec[$odr_value->rider_id]['max_pick']        = $odr_value->max_pick;
                        //     $rec[$odr_value->rider_id]['max_drop_size']   = $odr_value->max_drop_size;
                        //     $rec[$odr_value->rider_id]['max_drop_weight'] = $odr_value->max_drop_weight;
                        // }?>


                        @foreach($orders as $odr_key => $odr_value)
                        
                        
                        
                            @if(!(isset($odr_value->rider_id)) && ( ($odr_value->rider_id) == null))
                              
                                <?php 
                                    $msg        = "Please select rider";
                                    $tr_color   = "yellow";
                                    $td_color   = "";
                                ?>

                            @else
                                <?php 
                                    $msg        = "";
                                    $tr_color   = "";
                                    $td_color   = ($odr_value->color_code);
                                ?>
                            @endif
                                    
                            <tr style="background-color: {{$tr_color}}"> 
                                <td>
                                    <div class="checkbox-inline"> 
                                        <label class="checkbox checkbox-success">
                                            <input type="checkbox" name="route_id[{{$odr_value->id}}]" id ="route_id[{{$odr_value->id}}]"  />
                                            <span></span> 
                                        </label>
                                    </div>
                                </td>
                                <th >
                                    {{ Form::hidden('order_id['.$odr_value->id.']', $odr_value->id, array('readonly'=>'true')) }}
                                    {{$odr_value->order_id}} <br>
                                    <?php if($msg != ""){?>
                                        <span class="btn btn-secondary btn-sm"  data-toggle="tooltip" data-placement="top" title="<?php echo $msg?>">
                                            <i class="fas fa-question-circle" ></i></a>
                                        </span>
                                    <?php }?>  
                                </th>
                                <th >
                                    {{ Form::hidden('customer_id['.$odr_value->id.']', $odr_value->customer_id, array('readonly'=>'true')) }}
                                    {{$odr_value->customer_name}}  
                                </th>
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
                                        {{$odr_value->cus_address}}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($odr_value->cus_address))
                                        {{$odr_value->area_name}}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($odr_value->cus_address))
                                        {{$odr_value->zone_name}}
                                    @endif
                                </td>
                                <td style="background-color: {{$td_color}}">
                                    <select class="form-control" name = "rider_id[{{$odr_value->id}}]" id = "rider_id[{{$odr_value->id}}]">
                                
                                            @if(($odr_value->assign_rider) == null)
                                                <option value = "0" selected> -- Not selected -- </option>
                                            @endif
                                            @if((isset($odr_value->primary_rider)) && (($odr_value->primary_rider) !=0))
                                                <optgroup label="Primary" data-max-options="1" >
                                                    @foreach($odr_value->primary_rider as $p_key => $p_val)
                                                        <option value="{{$p_val['id']}}" <?php  echo $sel = ($p_val['id']== ($odr_value->assign_rider)) ? "selected": ""; ?> > {{$p_val['name']}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif

                                            @if((isset($odr_value->secondary_rider)) && (($odr_value->secondary_rider) !=0))
                                                <optgroup label="Secondary">
                                                    @foreach($odr_value->secondary_rider as $s_key => $s_val)
                                                        <option value="{{$s_val['id']}}"  <?php  echo $sel = ($s_val['id']== ($odr_value->assign_rider)) ? "selected": ""; ?>> {{$s_val['name']}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif

                                            @if((isset($odr_value->hanger_rider)) && (($odr_value->hanger_rider) !=0))
                                                
                                                <?php $p_chk = false; $s_chk = false; $inc =1;?>
                                                    @foreach($odr_value->hanger_rider as $h_key => $h_val)
                                                        <?php 
                                                            foreach($odr_value->primary_rider as $p_key => $p_val){
                                                                if(($h_val['id'])  != ($p_val['id'])){
                                                                    $p_chk = true; break; 
                                                                }  
                                                            }
                                                        ?>
                                                        <?php 
                                                            foreach($odr_value->secondary_rider as $s_key => $s_val){
                                                                if(($h_val['id'])  != ($s_val['id'])){
                                                                    $chk = true; break; 
                                                                }  
                                                            }
                                                        ?>

                                                        @if (($p_chk) && ($s_chk) )
                                                            @if(inc ==1)
                                                                <?php $inc++;?>
                                                                <optgroup label="Hanger">
                                                            @endif
                                                            <option value="{{$h_val['id']}}"  <?php  echo $sel = ($h_val['id']== ($odr_value->assign_rider)) ? "selected": ""; ?>> {{$h_val['name']}}</option>
                                                            
                                                        @endif
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        
                                    </select>
                                </td> 
                               
                                <td>
                                    <a href="javascript:void(0)"  data-id="{{$odr_value->id}}" class=" btn btn-secondary btn-sm route_cls">
                                        <i class="icon-xl far fa-window-close text-danger"></i>
                                    </a>
                                
                                </td>
                            </tr>
                        @endforeach
                        <?php 
                    }else{?>
                        <tr>
                            <td colspan="13">"<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2></td>
                        </tr> <?php
                    }
                ?> 
            </tbody>
        </table>
    </div> 
</form>
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