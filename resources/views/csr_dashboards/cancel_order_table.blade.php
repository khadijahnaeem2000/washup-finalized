<?php $schedule_order = 0; $un_schedule_order = 0; $i=0;?>
<?php if(isset($orders)) {?> 
   <?php foreach($orders as $key =>$value){ if(($dt==$value->delivery_date)&&($value->status_id==1)){$style="background-color: yellow";$un_schedule_order++;}elseif(!(isset($value->timeslot_id))){$style="background-color: yellow";$un_schedule_order++;}else{$style="";$schedule_order++;}?><tr style= "{{$style}}"><td width="2%">
                <div class="checkbox-inline"> 
                    <label class="checkbox checkbox-success">
                        <input type="checkbox" name="row_id[{{$value->id}}]" />
                        <span></span> 
                    </label>
                </div>
            </td>
            <td style="width:10%">
                @if(isset($value->id)) 
                    {{$value->id}}
                    {!! Form::hidden('id['.$value->id.']', $value->id, array('class' => 'form-control','required')) !!}
                @endif
            </td>
            <td style="width:15%">
                @if(isset($value->customer_name))
                    {{$value->customer_name}}
                    {!! Form::hidden('customer_id['.$value->id.']', $value->customer_id, array('class' => 'form-control','required')) !!}
                @endif
            </td>
            <td style="width:10%">
                @if(isset($value->customer_contact_no))
                    {{$value->customer_contact_no}}
                @endif
            </td>
            <td style="width:15%">
                <?php $i++;
                    if($i==1){?>
                        <script type="text/javascript">
                            var incre= 0;
                            $(document).ready(function(){
                                // restricting past dates
                                var disableSpecificDates = fetch_holidays_cancel() ;
                                $('.dpicker').datepicker({
                                    startDate: new Date(),
                                    format: 'yyyy/mm/dd',
                                    daysOfWeekDisabled: [0],
                                    beforeShowDay: function(date){
                                        dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                                        if(disableSpecificDates.indexOf(dmy) != -1){
                                            return false;
                                        }else{
                                            return true;
                                        }
                                    }
                                });
                            });

                            // formating date
                            function format_date_cancel(date) { 
                                var day     = date.getDate(); 
                                var month   = date.getMonth() + 1; 
                                var year    = date.getFullYear(); 
                                var myDate  = day + "-" + month + "-" + year; 
                                return myDate;
                            }

                            // fetching holidays  
                            function fetch_holidays_cancel(){
                                var hDays       = new Array();
                                var holidays    = {!! json_encode($holidays->toArray()) !!};
                                holidays.forEach(function(rec,index) {
                                    hDays[index] = format_date_cancel(new Date(rec.holiday_date)); 
                                }); 
                                return hDays;
                            }

                            function fn_delivery_date_cancel(pickup_date,inc,id){
                                this.incre= 0;
                                for(var i=1; i<=inc; i++){
                                    this.incre++;
                                    calc_delivery_date_cancel(pickup_date,this.incre,id);
                                    
                                }

                            }
                            
                            // calc_delivery_date_cancel(new Date());
                            function calc_delivery_date_cancel(pickup_date,incre,id){
                                var today       = new Date(pickup_date);
                                var finalDate   = new Date(today);

                                finalDate.setDate(today.getDate() + this.incre);
                                var temp        = new Date(finalDate);
                                // console.log(temp);

                                if( temp.getDay() == 0) {
                                    this.incre = this.incre +1;
                                    // console.log("getDay "+incre);
                                    calc_delivery_date_cancel(pickup_date,this.incre,id);
                                }else{
                                    var check       = 0 ;
                                    var disableSpecificDates = fetch_holidays_cancel() ;
                                    year            = temp.getFullYear();
                                    day             = temp.getDate() ;
                                    month           = temp.getMonth();
                                    month           = month+1;
                                    // day             = ('0' + day).slice(-2);
                                    // month           = ('0' + month).slice(-2);
                                    var delivery_date = year+'/'+month+'/'+day;
                                    
                                    var test_date   = temp.getDate()+'-'+month+'-'+year; 
                                    disableSpecificDates.forEach(function(rec,index) {
                                        if(rec == test_date){
                                            check = 1;
                                        }
                                    }); 
                                    if(check ==1){
                                        this.incre = this.incre +1;
                                        calc_delivery_date_cancel(pickup_date,this.incre,id);
                                    }else{
                                        $('#delivery_date_'+id).val(delivery_date);
                                    }
                                }
                            }
                        </script> 
                <?php }?>
                @if(isset($value->pickup_date))
                    @if($value->status_id==1)
                        {!! Form::text('pickup_date['.$value->id.']', $value->pickup_date, array('id'=>'pickup_date_'.$value->id,'readonly'=>'true','class' => 'form-control dpicker','required','style'=>'width: max-content;padding: 0 5px;','onchange'=>'fn_delivery_date_cancel(this.value,3,'.$value->id.')')) !!}
                    @else
                        {!! Form::text('pickup_date['.$value->id.']', $value->pickup_date, array('id'=>'pickup_date_'.$value->id,'readonly'=>'true','class' => 'form-control','required','style'=>'width: max-content;padding: 0 5px;background-color: #e6e6e6;')) !!}
                    @endif
                @endif
            </td>
            <td style="width:13%">
                
                @if(isset($value->delivery_date))
                    {!! Form::text('delivery_date['.$value->id.']', $value->delivery_date, array('id'=>'delivery_date_'.$value->id,'readonly'=>'true','class' => 'form-control dpicker','required','style'=>'width: max-content;padding: 0 5px;')) !!}
                @endif
            </td>
            <td style="width:13%">
                @if(isset($value->status_id))
                    @if($value->status_id!=1)
                        {!! Form::select('status_id['.$value->id.']',$cus_statuses, $value->status_id, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                    @elseif( $value->status_id ==1)
                        {!! Form::select('status_id['.$value->id.']',$statuses, $value->status_id, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                    @else
                        {!! Form::select('status_id['.$value->id.']',$all_statuses, $value->status_id, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                    @endif
                @endif
            </td>
            <td >
                @if(($dt ==$value->delivery_date) && ($value->timeslot_id == NULL))
                    {!! Form::select('timeslot_id['.$value->id.']',['0'=>' -- Not selected --']+$time_slots, 0, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                @elseif(isset($value->timeslot_id))
                    {!! Form::select('timeslot_id['.$value->id.']',$time_slots, $value->timeslot_id, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                @else
                    {!! Form::select('timeslot_id['.$value->id.']',['0'=>' -- Not selected --']+$time_slots, $value->timeslot_id, array('class' => 'form-control','required'=>'true','style'=>'width: max-content;padding: 0 5px;' )) !!}
                @endif
            </td>
            @if($value->order_note !=NULL)
                <td style="width:5%">
                    {{ 'Y' }}
                </td>
            @else
                <td style="background-color: red; color:white; font-weight: bold;width:5%">
                    {{ 'N' }}
                </td>
            @endif

            @if($value->polybags_printed !=NULL)
                <td style="width:5%">
                    {{ 'Y' }}
                </td>
            @else
                <td style="background-color: red; color:white; font-weight: bold;width:5%">
                    {{ 'N' }}
                </td>
            @endif
        
            <td style="width:5%">
                <div class="d-flex justify-content-end">
                    <div class="dropdown dropdown-inline">
                        <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ki ki-bold-more-hor"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="">
                            <!--begin::Navigation-->
                            <ul class="navi navi-hover py-5">
                                <li class="navi-item">
                                    <a href="csr_dashboards/show_order/{{$value->id}}" target="_blank" class="navi-link">
                                        <span class="navi-icon">
                                            <i class="flaticon-eye"></i>
                                        </span>
                                        
                                        <span class="navi-text">View Order</span>
                                    </a>
                                </li>
                                <li class="navi-item">
                                    <a href="#" class="navi-link edit_order"   data-id="{{$value->id}}" data-toggle="modal" data-target="#edit_order">
                                        <span class="navi-icon">
                                            <i class="flaticon-edit"></i>
                                        </span>
                                        <span class="navi-text">Edit Order</span>
                                    </a>
                                </li>
                                @if($value->status_id==1)
                                    <li class="navi-item">
                                        <a href="#" class="navi-link delete_order" data-id="{{$value->id}}">
                                            <span class="navi-icon">
                                                <i class="flaticon-delete"></i>
                                            </span>
                                            <span class="navi-text">Delete Order</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                
            </td></tr><?php } }?>



