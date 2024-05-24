@extends('layouts.master')
@section('title','Wallet')
@section('content')
    @include( '../sweet_script')   
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit Transaction</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('customer_wallets.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['customer_wallets.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'autofocus' => '' ,'onkeyup'=>'show_details(this.value)')) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                        </div>
                  
                        <div class="row"> 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('wallet_reason_id','Reason')) !!}
                                    {!! Form::select('wallet_reason_id', $reasons,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('wallet_reason_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('wallet_reason_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::hidden('v_amount', null, array('class' => 'form-control','id'=>'v_amount')) }}
                                    {!! Html::decode(Form::label('in_amount','Voucher Amount: <span class="text-danger">*</span>')) !!}
                                    
                                    {{ Form::number('in_amount', null, array('placeholder' => 'Enter voucher amount','class' => 'form-control','onkeyup'=>'calc_total(this.value)')) }}
                                    @if ($errors->has('in_amount'))  
                                        {!! "<span class='span_danger'>". $errors->first('in_amount')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('current_bal','Current Balance: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('current_bal', null, array('placeholder' => 'Current balance','readonly' => 'true' ,'class' => 'form-control')) }}
                                    @if ($errors->has('current_bal'))  
                                        {!! "<span class='span_danger'>". $errors->first('current_bal')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('tot_bal','Total Wallet Amount: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('tot_bal', null, array('placeholder' => 'Total wallet amount','readonly' => 'true' ,'class' => 'form-control')) }}
                                    @if ($errors->has('tot_bal'))  
                                        {!! "<span class='span_danger'>". $errors->first('tot_bal')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('detail','Detail: ')) !!}
                                    {!! Form::textarea('detail', null, array('placeholder' => 'Detail','rows'=>5, 'class' => 'form-control' )) !!}
                                    @if ($errors->has('detail'))  
                                        {!! "<span class='span_danger'>". $errors->first('detail')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="card-footer">
                        <div class="row">
                            
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <!-- <button type="reset" class="btn btn-secondary">Reset</button> -->
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>

        
    <!-- fetching customer detail by contact no -->
    <script type="text/javascript">
       $(document).ready(function(){
            var contact_no  = document.getElementById('contact_no').value;  
            show_details(contact_no);
        });

        function calc_total(){
            var current_bal = $("input[name='current_bal']").val();
            var voucher_bal = $("input[name='in_amount']").val();
            var total       = (parseInt(current_bal) + parseInt(voucher_bal));
            $('#tot_bal').val(total);
        }

  

        function show_details($contact_no){
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_customer_detail') }}",
                method: 'POST',
                data: {contact_no:$contact_no, _token:token},
                success: function(data) {
                    if(data.data){
                        $('#name').val(data.data.name);
                        $('#customer_id').val(data.data.id);
                        var v_amount    = $("input[name='v_amount']").val();
                        $('#current_bal').val(data.current_bal - parseInt(v_amount));
                        $('#tot_bal').val(data.current_bal);
                        calc_total();
                    }else{
                        $('#name').val('');
                        $('#customer_id').val('');
                        $('#current_bal').val('');
                        $('#in_amount').val(0);
                        $('#tot_bal').val(0);
                    }
                }
            });
        }
        
    </script>
@endsection

