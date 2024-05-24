<div class="col-lg-6">
    <table class="table" id="">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center">Action-wise locations</th>
            </tr>
        </thead>
        <tbody>
            <?php $tot_loc= 0;
                if(isset($statuses)) {
                    foreach($statuses as $key =>$value){ ?><tr><td>Total {{$value->name}}</td>
                    <th><?php 
                        foreach ($sts as $k => $v) {
                            if(($value->id) == $k ){
                                echo $v; $tot_loc+=$v;
                            }
                        }?>
                    </th></tr><?php }?><tr><th>Total Locations</th><th><?php echo $tot_loc;?></th></tr>
                 <?php }?>
        </tbody>
    </table>
</div>
<div class="col-lg-6">
    <table class="table" id="">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center">Timeslot-wise locations</th>
            </tr>
        </thead>
        <tbody>
            <?php $tot_loc= 0;
                if(isset($timeslots)) {
                    foreach($timeslots as $key =>$value){ ?><tr><td>{{$value->name}}</td>
                    <th><?php foreach ($tms as $k => $v) {
                        if(($value->id) == $k ){
                            echo $v;$tot_loc+=$v;
                        }
                    }?>
                </th></tr><?php }?><tr><th>Total Locations</th><th><?php echo $tot_loc;?></th></tr>
             <?php }?>
        </tbody>
    </table>
</div>
