    <div class="col-md-12 offset-md-0">
        <table class="table" id="rider_table">
            <thead>
                <tr>
                    <th width="15%"></th>
                    <th width="10%">Vehicle type for day</th>
                    <th width="10%">Max: Loc per day</th>
                    <th width="10%">Max: Pick per route</th>
                    <th width="12%">Drop weight(Kg) per route</th>
                    <th width="12%">Drop Size(Kg) per order</th>
                    <th width="10%">Max: Route auto-calc</th>
                    <th width="16%">Zone</th>
                    <th width="5%">Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riders as $r_key => $r_value)
                    @if($r_value->status == 0)
                        <?php 
                            $readonly = "readonly";
                            $state    = "";
                        ?>
                    @else
                        <?php 
                            $readonly = "";
                            $state    = "checked";
                        ?>
                    @endif
                    <tr>
                        <th >
                            <input type ="text" hidden name = "rider[{{$r_value->id}}]" value="{{$r_value->id}}" class="form-control">
                            {{$r_value->id}} - {{$r_value->name}} 
                        </th>
                        <td>
                            {!! Form::select('vehicle_type_id['.$r_value->id.']',$vehicle_types,$r_value->vehicle_type_id, array('class' => 'form-control')) !!}
                        </td>
                        <td> 
                            <input min = "1" required type ="number" name = "max_loc[{{$r_value->id}}]" value = "{{$r_value->max_loc}}" id = "max_loc[{{$r_value->id}}]" class="form-control">
                        </td>
                        <td> 
                            <input min = "1" required  type ="number"  name = "max_pick[{{$r_value->id}}]" value = "{{$r_value->max_pick}}" id = "max_pick[{{$r_value->id}}]" class="form-control">
                        </td>
                        <td> 
                            <input min = "1" required type ="number"  name = "max_drop_weight[{{$r_value->id}}]" value = "{{$r_value->max_drop_weight}}" id = "max_drop_weight[{{$r_value->id}}]" class="form-control">
                        </td>
                        <td> 
                            <input min = "1" required type ="number"  name = "max_drop_size[{{$r_value->id}}]" value = "{{$r_value->max_drop_size}}" id = "max_drop_size[{{$r_value->id}}]" class="form-control"> 
                        </td>
                        <td> 
                            <input min = "1" required type ="number"   name = "max_route[{{$r_value->id}}]" value = "{{$r_value->max_route}}" id = "max_route[{{$r_value->id}}]" class="form-control" readonly>
                        </td>
                        <td>
                            <?php foreach($rider_zones as $key => $val){
                                if($r_value->id == $val->rider_id){?>
                                    <?php if($val->zone_type == 'Primary'){?>
                                        <span class="tBlue">  {{$val->name}} ({{$val->zone_type}}) </span>  
                                    <?php }else{?>
                                        <span class="tGreen">    {{$val->name}} ({{$val->zone_type}}) </span> 
                                    <?php }?>
                            <?php }}?>
                        </td>
                        <td>
                            <div class="form-group">
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        <input type ="checkbox" name = "state[{{$r_value->id}}]" value="1" class="form-control" {{$state}}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
</form>