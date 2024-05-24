
<script type="text/javascript">
    var rec=[];
    var dt = '';
        // Defining Vairables
    var id, de,pre,cur,exp_dist=0,pre_index = 0,cur_index = 0, inc=1, inx = 0;
    var tot_dist = 0, tot_time = 0;
    var all_dist    = [];
    var sr          = '';
    var increment   = 0 ;

    // storing PHP Array to Js
    function fn_set_array(){
        increment   = 0 ;
        <?php 
        // dd($rds);
            foreach ($rds as $rds_key => $rds_value) {
                
                
                foreach ($rds_value as $rt_key => $rt_value) {?>
                    rec = []; <?php
                        
                        // echo $rt_value[$rds_key]->route;
                        //     if(array_key_exists($rt_value[$rds_key]->route, $rt_value)){
                    foreach ($rt_value as $key => $value) {?>
                        console.log(<?php echo $value->id  ?>)
                        dt = {
                            'id': "<?php echo $value->id;?>",
                            'route': "<?php echo $value->route;?>",
                            'rider_id': "<?php echo $value->rider_id;?>",
                            'latitude': "<?php echo $value->latitude;?>",
                            'longitude': "<?php echo $value->longitude;?>",
                            'hub_latitude': "<?php echo $value->hub_latitude;?>",
                            'hub_longitude': "<?php echo $value->hub_longitude;?>"
                        } 
                        rec.push(dt);<?php
                        unset($rds[$rds_key][$rt_key][$key]);
                    }
                    
                    ?>
                    console.log(rec);
                    console.log("increment: " + increment);
                    console.log("id: " + rec[increment]['id']);
                    sr = rec[increment]['hub_latitude'] +"," +rec[increment]['hub_longitude'];
                    increment++;
                    // fn_get_route();
                    <?php        
                    break;
                }
                break;
            }
        ?>
        
    }
</script>