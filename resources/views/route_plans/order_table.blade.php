<script>
    $(document).ready(function () { 
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
    });
</script>
<div class="col-md-12 offset-md-0">
        <table class="table" id="order_table">
            <thead>
                <tr>
                    <th width="5%">Order#</th>
                    <th width="5%">Customer#</th>
                    <th width="10%">Action</th>
                    <th width="5%">Type</th>
                    <th width="5%">Weight</th>
                    <th width="5%">Hanger</th>
                    <th width="10%">TimeSlot</th>
                    <th width="20%">Address</th>
                    <th width="7%">Area</th>
                    <th width="8%">Zone</th>
                    <th width="15%">Rider</th>
                    <th width="5%">Route</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $seq        = 0;
                    $pre_rider  = $orders[0]->rider_id;
            
                    foreach($orders as $odr_key => $odr_value){
                        $rec[$odr_value->rider_id]['route']           = 1;
                        $rec[$odr_value->rider_id]['drop_size']       = 0;
                        $rec[$odr_value->rider_id]['drop_weight']     = 0;
                        $rec[$odr_value->rider_id]['pick']            = 0;
                        $rec[$odr_value->rider_id]['tot_loc']         = 0;

                        $rec[$odr_value->rider_id]['max_pick']        = $odr_value->max_pick;
                        $rec[$odr_value->rider_id]['max_drop_size']   = $odr_value->max_drop_size;
                        $rec[$odr_value->rider_id]['max_drop_weight'] = $odr_value->max_drop_weight;
                        $rec[$odr_value->rider_id]['max_loc']         = $odr_value->max_loc;
                    }
                ?>


                @foreach($orders as $odr_key => $odr_value)
                    <?php 
                            // Increment in tot_loc 
                            $rec[$odr_value->rider_id]['tot_loc']++;

                            $cur_rider                                  = $odr_value->rider_id;
                            $msg                                        = "";
                            $tr_color                                   = "";
                            $td_color                                   = "";
                            $rec[$odr_value->rider_id]['drop_size']     = ($odr_value->weight);  // drop size is for one order
                            $rec[$odr_value->rider_id]['drop_weight']  += ($odr_value->weight);  // drop weight is for one route 
                            if(($odr_value->status_name) == 'Pickup'){
                                $rec[$odr_value->rider_id]['pick']     = ($rec[$odr_value->rider_id]['pick'] + 1);
                            }
                    ?>
                 
                    @if(!(isset($odr_value->rider_id)) && ( ($odr_value->rider_id) == null))
                        @if(($rec[$odr_value->rider_id]['drop_size']) >  $rec[$odr_value->rider_id]['max_drop_size'] )
                            <?php 
                                $msg        = "Order weight exceeds the riders drop size";
                                $tr_color   = "#e6e6e6";
                                $td_color   = "";
                            ?>
                      
                         @else
                            <?php 
                                $msg        = "Please select rider";
                                $tr_color   = "yellow";
                                $td_color   = "";
                            ?>
                        @endif

                    @elseif( (isset($odr_value->rider_id)) && (($odr_value->rider_hanger) == 0) && (($odr_value->hanger) == 1) )
                        <?php 
                            $msg            = "Please select hanger rider";
                            $tr_color       = "yellow";
                            $td_color       = "";
                        ?>
                  
                    @elseif(($rec[$odr_value->rider_id]['drop_size']) >  $rec[$odr_value->rider_id]['max_drop_size'] )
                        <?php 
                            $msg        = "Order weight exceeds the riders drop size";
                            $tr_color   = "#e6e6e6";
                            $td_color   = "";
                        ?>
                    @elseif(($rec[$odr_value->rider_id]['tot_loc']) >  $rec[$odr_value->rider_id]['max_loc'] )
                    <?php 
                        $msg        = "Please select another rider! max location exceeded";
                        $tr_color   = "#A0EBFF";
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
                        <th>
                           
                            {{ Form::hidden('order_id['.$odr_value->id.']', $odr_value->id, array('readonly'=>'true')) }}
                            {{ Form::hidden('odr_id['.$odr_value->id.']', $odr_value->order_id, array('readonly'=>'true')) }}
                            {{$odr_value->order_id}} <br>
                            <!-- {{$odr_value->id}}  -->
                            <?php if($msg != ""){?>
                               
                                <span class="btn btn-secondary btn-sm" data-container="body" data-toggle="tooltip" data-placement="right" title="<?php echo $msg?>">
                                    <i class="fas fa-question-circle" ></i></a>
                                </span>
                            <?php }?>  
                        </th>
                        <th >
                            {{ Form::hidden('customer_id['.$odr_value->id.']', $odr_value->customer_id, array('readonly'=>'true')) }}
                            {{$odr_value->customer_name}}  
                        </th>
                        <td>
                            <input type = "text" name = "dist[{{$odr_value->id}}]" value=""  id = "dist[{{$odr_value->id}}]" class="form-control" >
                            <input type = "text" name = "time[{{$odr_value->id}}]" value="" id = "time[{{$odr_value->id}}]" class="form-control" >
                            <input type = "text" name = "seq[{{$odr_value->id}}]" value="" id = "seq[{{$odr_value->id}}]" class="form-control" >
                            <input type = "text" name = "address[{{$odr_value->id}}]" value="" id = "address[{{$odr_value->id}}]" class="form-control" >

                            {{ Form::hidden('status_name['.$odr_value->id.']', $odr_value->status_name, array('readonly'=>'true')) }}
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
                            @if(isset($odr_value->area_name))
                                {{$odr_value->area_name}}
                            @endif
                        </td>
                        <td>
                            @if(isset($odr_value->zone_name))
                                {{$odr_value->zone_name}}
                            @endif
                            
                        </td>
                        <td style="background-color: {{$td_color}}">
                            <select class="form-control" name = "rider_id[{{$odr_value->id}}]" id = "rider_id[{{$odr_value->id}}]">
                           
                                    @if(($odr_value->assign_rider) == null)
                                        <option value = "0" selected> -- Not selected -- </option>
                                    @endif
                                    @if((count($odr_value->primary_rider) > 0 ) && (($odr_value->primary_rider) !=0))
                                        <optgroup label="Primary" data-max-options="1" >
                                            @foreach($odr_value->primary_rider as $p_key => $p_val)
                                                <option value="{{$p_val['id']}}" <?php  echo $sel = ($p_val['id']== ($odr_value->assign_rider)) ? "selected": ""; ?> > {{$p_val['name']}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if( (count($odr_value->secondary_rider) > 0 ) && (($odr_value->secondary_rider) !=0))
                                        <optgroup label="Secondary">
                                            @foreach($odr_value->secondary_rider as $s_key => $s_val)
                                                <option value="{{$s_val['id']}}"  <?php  echo $sel = ($s_val['id']== ($odr_value->assign_rider)) ? "selected": ""; ?>> {{$s_val['name']}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if((count($odr_value->hanger_rider) > 0 ) && (($odr_value->hanger_rider) !=0))
                                    <?php $inc =1;?>
                                        @foreach($odr_value->hanger_rider as $h_key => $h_val)
                                            <?php $p_chk = false; $s_chk = false; ?>
                                                <?php
                                                   
                                                    if((count($odr_value->primary_rider) > 0 ) && (($odr_value->primary_rider) !=0)){ 
                                                        foreach($odr_value->primary_rider as $p_key => $p_val){
                                                            if(($h_val['id'])  == ($p_val['id'])){
                                                                $p_chk = true; break; 
                                                            }  
                                                        }
                                                    }
                                                ?>
                                                <?php 
                                                   if((count($odr_value->secondary_rider) > 0 ) && (($odr_value->secondary_rider) !=0)){
                                                        foreach($odr_value->secondary_rider as $s_key => $s_val){
                                                            if(($h_val['id'])  == ($s_val['id'])){
                                                                $s_chk = true; break; 
                                                            }  
                                                        }
                                                    }
                                                ?>

                                                @if((!$p_chk) && (!$s_chk) )
                                                    @if($inc ==1)
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
                        <?php 
                            $ls_route = $rec[$odr_value->rider_id]['route'];
                            if((($rec[$odr_value->rider_id]['drop_weight']) >  ($rec[$odr_value->rider_id]['max_drop_weight'] )) ){
                                $ls_route++;

                                $rec[$odr_value->rider_id]['route']         = ($rec[$odr_value->rider_id]['route'] + 1);
                                $rec[$odr_value->rider_id]['drop_weight']   = 0;

                            }elseif((($rec[$odr_value->rider_id]['pick']) >  ($rec[$odr_value->rider_id]['max_pick'] ))){
                                $ls_route++;
                                $rec[$odr_value->rider_id]['route']         = ($rec[$odr_value->rider_id]['route'] + 1);
                                $rec[$odr_value->rider_id]['pick']          = 1;
                            }
                        ?>

                        <?php 
                            if($pre_rider == $cur_rider){
                                $seq++;
                            }else{
                                $pre_rider = $cur_rider;
                                $seq = 1;
                            }
                        ?>
                        <td>
                            {{ Form::text('route_no['.$odr_value->id.']',$ls_route, array('readonly'=>'true', 'class' => 'form-control')) }}
                            <!-- {{ Form::text('seq1['.$odr_value->id.']',$seq, array('readonly'=>'true', 'class' => 'form-control')) }} -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
</form>