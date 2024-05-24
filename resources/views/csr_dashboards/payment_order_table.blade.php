<?php if(isset($orders)){$srno=1; $i=0;
    foreach($orders as $key =>$value){?><tr><td width="2%">
                <div class="checkbox-inline"> 
                    <label class="checkbox checkbox-success">
                        <input type="checkbox" name="customer_id[{{$value->customer_id}}]" />
                        <span></span> 
                    </label>
                </div>
            </td>
            <td width="3%"> 
                <?php echo $srno++; $i++; if($i==1){?>
                    <script type="text/javascript">
                        var incre= 0;
                        $(document).ready(function(){
                            // restricting past dates
                            var disableSpecificDates = fetch_holidays_pay() ;
                            $('.dpicker').datepicker({
                                startDate: new Date(),
                                format: 'yyyy-mm-dd',
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
                        function format_date_pay(date) { 
                            var day     = date.getDate(); 
                            var month   = date.getMonth() + 1; 
                            var year    = date.getFullYear(); 
                            var myDate  = day + "-" + month + "-" + year; 
                            return myDate;
                        }

                        // fetching holidays  
                        function fetch_holidays_pay(){
                            var hDays       = new Array();
                            var holidays    = {!! json_encode($holidays->toArray()) !!};
                            holidays.forEach(function(rec,index) {
                                hDays[index] = format_date_pay(new Date(rec.holiday_date)); 
                            }); 
                            return hDays;
                        }

                        function fn_delivery_date_pay(pickup_date,inc,id){
                            this.incre= 0;
                            for(var i=1; i<=inc; i++){
                                this.incre++;
                                calc_delivery_date_pay(pickup_date,this.incre,id);
                                
                            }

                        }
                        
                        // calc_delivery_date_pay(new Date());
                        function calc_delivery_date_pay(pickup_date,incre,id){
                            var today       = new Date(pickup_date);
                            var finalDate   = new Date(today);

                            finalDate.setDate(today.getDate() + this.incre);
                            var temp        = new Date(finalDate);
                            // console.log(temp);

                            if( temp.getDay() == 0) {
                                this.incre = this.incre +1;
                                // console.log("getDay "+incre);
                                calc_delivery_date_pay(pickup_date,this.incre,id);
                            }else{
                                var check       = 0 ;
                                var disableSpecificDates = fetch_holidays_pay() ;
                                year            = temp.getFullYear();
                                day             = temp.getDate() ;
                                month           = temp.getMonth();
                                month           = month+1;
                                // day             = ('0' + day).slice(-2);
                                // month           = ('0' + month).slice(-2);
                                var delivery_date = year+'-'+month+'-'+day;
                                
                                var test_date   = temp.getDate()+'-'+month+'-'+year; 
                                disableSpecificDates.forEach(function(rec,index) {
                                    if(rec == test_date){
                                        check = 1;
                                    }
                                }); 
                                if(check ==1){
                                    this.incre = this.incre +1;
                                    calc_delivery_date_pay(pickup_date,this.incre,id);
                                }else{
                                    $('#delivery_date_'+id).val(delivery_date); 
                                }
                            }
                        }
                        
                    </script>
                <?php }?>
            </td>
            <td width="15%" >
                @if(isset($value->customer_name))
                    {{$value->customer_name}}
                @endif
            </td>
            <td width="10%" >
                @if(isset($value->customer_contact_no))
                    {{$value->customer_contact_no}}
                @endif
            </td>
            <td width="20%" >
                @if(isset($value->address))
                    {{$value->address}}
                    {{ Form::hidden('address_id['.$value->customer_id.']',$value->address_id, array( 'class' => 'form-control','required'=>'true')) }}
                @endif
            </td>
            <td width="10%" >
                @if(isset($value->bill))
                    {{$value->bill}}
                    {{ Form::hidden('bill['.$value->customer_id.']',$value->bill, array( 'class' => 'form-control','required'=>'true')) }}
                @endif
            </td>
            <td width="15%" >
                {{ Form::text('ride_date['.$value->customer_id.']',$dt, array( 'class' => 'form-control dpicker','required'=>'true','readonly'=>'true')) }}
            </td>
            
            <td width="15%" >
                {!! Form::select('timeslot_id['.$value->customer_id.']',$time_slots, null, array('class' => 'form-control','required'=>'true' )) !!}
            </td></tr><?php }} ?>




