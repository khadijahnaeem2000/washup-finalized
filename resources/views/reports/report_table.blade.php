<?php if(isset($rec)) { $i=0; $s_dist = 0; $s_loc = 0; $s_polybags = 0;  $a_dist = 0; $a_loc = 0; $a_polybags = 0; $tot_receiving = 0; ?>

   <?php foreach($rec as $key =>$value){ $i++; ?><tr><td style="width:5%">

                    {{$i}}

            </td>

            <td style="font-weight:bold">

                @if(isset($value->plan_date))

                    {{$value->plan_date}}

                @endif

            </td>

            <td>

                @if(isset($value->rider_name))

                    {{$value->rider_name}}

                    

                @endif

            </td>

            <td>

                @if((isset($value->s_dist)))

                    <?php $s_dist += $value->s_dist; ?>

                    {{round($value->s_dist,1)}} Kms

                @endif

            </td>

            <td>

                @if((isset($value->s_loc))  )

                    <?php $s_loc += $value->s_loc; ?>

                    {{$value->s_loc}}

                @endif

            </td>

            <td>

                @if((isset($value->s_polybags)) )

                    <?php $s_polybags += $value->s_polybags; ?>

                    {{$value->s_polybags}}

                @endif

            </td>

            <td>

                @if((isset($value->a_dist))  )

                    <?php $a_dist += $value->a_dist; ?>

                    {{round($value->a_dist,1)}} Kms

                @endif

            </td>

            <td>

                @if((isset($value->a_loc)) )

                    <?php $a_loc += $value->a_loc; ?>

                    {{$value->a_loc}}

                @endif

            </td>

            <td>

                @if((isset($value->a_polybags))  )

                    <?php $a_polybags += $value->a_polybags; ?>

                    {{$value->a_polybags}}

                @endif

            </td>

            <td>

                @if((isset($value->receiving)))

                    <?php $tot_receiving += $value->receiving; ?>

                    {{$value->receiving}}

                @endif

            </td>

            <td>

                <?php

                    $id     = strtotime($value->date)."-".$value->rider_id; 

                    $rec    = explode("-",$id);

                ?>

                <div class="btn-group btn-group">

                    <a class="btn btn-secondary btn-sm" href="reports/{{$id}}/edit">

                        <i class="fas fa-eye"></i>

                    </a>

                </div>

            </td>

        </tr><?php }?><tr><td></td>

            <th>Total</th>

            <td></td>

            <th>{{round($s_dist,1)}}  Kms</th>

            <th>{{$s_loc}}</th>

            <th>{{$s_polybags}}</th>

            <th>{{round($a_dist,1)}} Kms</th> 

            <th>{{$a_loc}}</th>

            <th>{{$a_polybags}}</th>

            <th>{{$tot_receiving}}</th>

            <td></td></tr><?php }?>







