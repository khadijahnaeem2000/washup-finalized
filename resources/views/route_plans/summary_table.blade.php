<div class="col-md-6 offset-md-3"><h4 style="text-align:center; font-weight:bold; ">Action-wise locations</h4></div>
<div class="col-md-10 offset-md-1">
    <table class="table" id="">
        <tbody>
            <?php if(isset($statuses)) { $tot = 0; 
                foreach ($statuses as $s_key => $s_value) {?>
                   <tr><td>{{$s_value->name}}</td><th>
                    <?php 
                        if($act_orders->count() >0) {   $val =0;
                            foreach($act_orders as $key =>$value){ 
                                if(($s_value->id) == ($value->id)){
                                    $val  =  $value->total;
                                    $tot += ($value->total);
                                }
                            }
                            echo $val;
                        }else{
                            echo '0';
                        }
                    ?> 
                    </th></tr>
            <?php }}?> 
        </tbody>
        <tfoot>
                <tr  style="background: #e6e6e6;" >
                    <th>Total Location </th>
                    <th>{{$tot}}</th>
                </tr>
        </tfoot>
    </table>
</div> 
<div class="col-md-6 offset-md-3"><h4 style="text-align:center; font-weight:bold">Timeslot-wise locations</h4></div>
<hr>
<div class="col-md-10 offset-md-1">
    <table class="table" id="">
        <thead>
            <tr>
                <th> Zones </th>
                @if(isset($time_slots))
                    @foreach($time_slots as $t_key => $t_value)
                        <th class="data_center">{{$t_value}}</th>
                    @endforeach
                @endif
                <th class="data_center">Total Locations</th>
            </tr>
        </thead>
        <?php $tm_tot = array();
            foreach ($time_slots as $key => $value) {
                $tm_tot[$key]=0;
            }
        ?>
        <tbody>
            @if(isset($zones))
               
                @foreach ($zones as $z_key => $z_value) <?php $tot = 0;?>
                    <tr>
                        <td >{{$z_value}} </td>
                        @foreach($zn_orders[$z_key] as $t_key => $t_value)
                            <th class="data_center">{{$t_value}}</th> <?php $tot+=$t_value; $tm_tot[$t_key]+= $t_value;?>
                        @endforeach
                        <th style="background: #e6e6e6;" class="data_center">{{$tot}}</th>
                    </tr>
                @endforeach
                
            @endif
        </tbody>
        <tfoot>
            <tr style="background: #e6e6e6;">
                <th>Total Locations</th> <?php $tot = 0;?>
                @foreach ($tm_tot as $key =>$value) <?php $tot += $value; ?>
                    <th class="data_center">{{$value}}</th>
                @endforeach
                <th class="data_center">{{$tot}}</th>
            </tr>
        </tfoot>
    </table>
</div>




<div class="col-md-6 offset-md-3"><h4 style="text-align:center; font-weight:bold">Rider-wise locations</h4></div>
<hr>
<div class="col-md-10 offset-md-1">
    <table class="table" id="">
        <thead>
            <tr>
                <th> Riders </th>
                @if(isset($time_slots))
                    @foreach($time_slots as $t_key => $t_value)
                        <th class="data_center">{{$t_value}}</th>
                    @endforeach
                @endif
                <th class="data_center">Total Locations</th>
            </tr>
        </thead>
        <?php $tm_tot = array();
            foreach ($time_slots as $key => $value) {
                $tm_tot[$key]=0;
            }
        ?>
        <tbody>
            @if(isset($riders))
               
                @foreach ($riders as $r_key => $r_value) <?php $tot = 0;?>
              
                    <tr>
                        <td> {{$r_value->name}} </td>
                            @foreach($rd_orders[$r_value->id] as $t_key => $t_value)
                                <th class="data_center">{{$t_value}} </th> <?php $tot += $t_value; $tm_tot[$t_key]+= $t_value;?>
                            @endforeach
                        <th style="background: #e6e6e6;" class="data_center">{{$tot}} /  {{$r_value->max_loc}} </th>
                    </tr>
                @endforeach
                
            @endif
        </tbody>
        <tfoot>
            <tr style="background: #e6e6e6;">
                <th>Total Locations</th> <?php $tot = 0;?>
                @foreach ($tm_tot as $key =>$value) <?php $tot+=$value; ?>
                    <th class="data_center">{{$value}}</th>
                @endforeach
                <th class="data_center">{{$tot}}</th>
            </tr>
        </tfoot>
    </table>
</div>

