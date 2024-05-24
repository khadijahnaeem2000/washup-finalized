<div class="col-md-12">
    <table class="table" id="plan_table">
        <thead>
            <tr>
                <th>Plan Date</th>
                <th>Rider</th>
                <th>Total location</th>
                <th>Total picks</th>
                <th>Total drops</th>
                <th>Total pick & drop </th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if((isset($orders)) && (!($orders->isEmpty()))) {
                    foreach ($orders as $key => $value) {?>
                        <tr>
                            <td>{{$value->date}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{(($value->pick_up) + ($value->drop_off) + ($value->pick_drop))}}</td>
                            <td>{{$value->pick_up}}</td>
                            <td>{{$value->drop_off}}</td>
                            <td>{{$value->pick_drop}}</td>
                            <?php 
                                    $url = ($value->rider_id) .".". ($value->date);

                            ?>
                            <td> 
                                <a class="btn btn-secondary btn-sm" href="scheduled_plans/{{$url}}/edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr><?php 
                    }
                }else{?>
                    <tr>
                        <td colspan="7">"<h2 style='text-align:center; padding:10px'>!!! No Record Found !!! </h2></td>
                    </tr> <?php
                }
            ?> 
        </tbody>
    </table>
</div> 