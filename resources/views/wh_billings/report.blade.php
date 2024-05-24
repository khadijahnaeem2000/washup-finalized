<?php if(isset($data)) { $grand_service_tot = 0; $grand_pickup_tot = 0;?> 
   <?php foreach($data as $key =>$items){ ?><table id="myTable" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0"><thead>
                                    <tr>
                                        <!-- <th colspan="4"> -->
                                        <th colspan="3">
                                        <?php 
                                        $service_name = '';
                                        foreach ($items as $kk => $vv) {
                                            $service_name = $vv['service_name'];
                                            break;
                                        }
                                        ?>
                                            <center>{{$service_name}}</center>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="40%">Item</th>
                                        <th width="20%">Pieces</th>
                                        <!-- <th width="20%">Rate</th> -->
                                        <th width="20%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php   
                                    $tot_pickup = 0; $tot = 0; $service_tot = 0;
                                    foreach($items as $k =>$val){ 
                                        $tot                = ($val['rate'] * $val['pickup_qty']);
                                        $service_tot        += $tot;
                                        $tot_pickup         += $val['pickup_qty'];
                                        $grand_service_tot  += $tot;
                                        $grand_pickup_tot   += $val['pickup_qty'];
                                    ?>
                                    <tr>
                                        <td>{{$val['item_name']}}</td>
                                        <td>{{$val['pickup_qty']}}</td>
                                        <!-- <td>{{$val['rate']}}</td> -->
                                        <td>{{$tot}}</td>
                                    </tr>
                                <?php }?>
                                </tbody>
                                <tfoot>
                                        <th>Total</th>
                                        <th>{{$tot_pickup}}</th>
                                        <!-- <th></th> -->
                                        <th>{{$service_tot}}</th>
                                </tfoot>
                            </table><br>
   <?php } }?><hr style="border-top: 2px solid black;"><table id="myTable" class="" style="width: 100%;" cellspacing="0"><thead><tr>
            <th width="40%">Grand Total</th>
            <th width="20%">{{$grand_pickup_tot}}</th>
            <!-- <th width="20%"></th> -->
            <th width="20%">{{$grand_service_tot}}</th>
        </tr>
    </table><hr style="border-top: 2px solid black;">
    <table class="table table-bordered dt-responsive">
        <thead>
            <tr>
                <th colspan="4"> <center>Addons</center></th>
            </tr>                     
            <tr>
                <td width="40%">Addon Name</td>
                <td width="20%">Qty</td>
                <!-- <td width="20%">Rate</td> -->
                <td width="20%">Total</td>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(isset($adn_record)){ $tot_adn_qty = 0; $adn_total = 0; $grand_adn_total = 0; 
                    foreach($adn_record as $key =>$adns) { 
                        $tot_adn_qty        += $adns['qty'];
                        $adn_total           = ($adns['rate']);
                        $grand_adn_total    += $adn_total;
                        ?>
                        <tr>
                            <td>{{$adns['addon_name']}}</td>
                            <td>{{$adns['qty']}}</td>
                            <!-- <td>{{$adns['rate']}}</td> -->
                            <td>{{$adn_total}}</td>
                        </tr>
                <?php }}?>
        </tbody>
        <tfoot>
            <th>Addon Total</th>
            <th>{{$tot_adn_qty}}</th>
            <!-- <th></th> -->
            <th>{{$grand_adn_total}}</th>
        </tfoot>
    </table><hr style="border-top: 2px solid black;"><table id="myTable" class="" style="width: 100%;" cellspacing="0"><thead><tr>
            <th width="40%">Total Billing</th>
            <th width="20%"></th>
            <!-- <th width="20%"></th> -->
            <th width="20%">{{($grand_service_tot + $grand_adn_total)}}</th>
        </tr>
    </table><hr style="border-top: 2px solid black;">



