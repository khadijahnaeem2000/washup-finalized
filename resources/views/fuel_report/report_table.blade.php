
<?php if(isset($rec)) { $i=0; $a_dist = 0;$a_loc = 0;$drop_off = 0;$pick_drop = 0;$pick_up = 0;$start_reading = 0;$end_reading  = 0;$meter_id =0; ?>

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

                @if((isset($value->start_reading)))

                    <?php $start_reading += $value->start_reading; ?>

                    {{round($value->start_reading,1)}} Kms

                @endif

            </td>

            <td>

                @if((isset($value->end_reading))  )

                    <?php $end_reading += $value->end_reading; ?>

                    {{$value->end_reading}}

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

                @if((isset($value->pick_up)) )

                    <?php $pick_up += $value->pick_up; ?>

                    {{$value->pick_up}}

                @endif

            </td>

            <td>

                @if((isset($value->drop_off))  )

                    <?php $drop_off += $value->drop_off; ?>

                    {{$value->drop_off}}

                @endif

            </td>

            <td>

                @if((isset($value->pick_drop)))

                    <?php $pick_drop += $value->pick_drop; ?>

                    {{$value->pick_drop}}

                @endif

            </td>

            <td>

                <?php

                    $id     = strtotime($value->date)."-".$value->rider_id; 

                    $rec    = explode("-",$id);

                ?>

                <div class="btn-group btn-group">
                    <a class="btn btn-secondary btn-sm" href="{{ route('fuel_reports_show', ['id' => $id]) }}">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
               <div class="btn-group btn-group">
    <button id="edit-btn-{{ $value->meter_id }}" class="btn btn-secondary btn-sm edit-btn" onclick="toggleEdit('{{ $value->meter_id }}', '{{ $value->rider_id }}', '{{ $value->plan_date }}','{{$id}}')">
        <i class="fas fa-pen"></i>
    </button>
</div>


                <div class="btn-group btn-group">
                    <button id="lock-btn-{{ $value->meter_id }}" class="btn btn-{{ $value->lockStatus ? 'danger' : 'secondary' }} btn-sm" onclick="toggleLock('{{ $value->meter_id }}')">
                        <i class="fas fa-lock"></i>
                    </button>
                </div>

            </td>

        </tr><?php }?><tr><td></td>

            <th>Total</th>

            <td></td>

            <th></th>

            <th></th>

            <th>{{round($a_dist,1)}} Kms</th> 

            <th>{{$a_loc}}</th>

            <th>{{$pick_up}}</th>

            <th>{{$drop_off}}</th>

            <th>{{$pick_drop}}</th>

            <td></td></tr><tr><td></td>

            <th></th>

            <td></td>

            <th></th>

            <th></th>

            <td>{{$i}}</td> 

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <td></td></tr><tr><td></td>

            <th>Total</th>

            <td></td>

            <th></th>

            <th></th>

            <td>{{round($a_dist,1)*$i}}</td> 

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <td></td></tr><?php }?>







