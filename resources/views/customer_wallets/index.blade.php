@extends('layouts.master')
@section('title','Wallet')
@section('content')
    @include( '../sweet_n_datatable_script')
    <style>
       #report_table th,#report_table td{
        vertical-align: middle;
            text-align: center;
        }
        #report_table th{
            font-weight:bold;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
        <!-- <div class="card card-custom gutter-b example example-compact search_div"> -->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header py-3">
                    {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data','style'=>'width:100%')) !!}
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('from_date','From Date: ')) !!}
                                    <input type="date" name = "from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('to_date','To Date: ')) !!}
                                    <input type="date" name = "to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('get_report_btn','Action')) !!}
                                    <a href="" id="get_report_btn" class="btn btn-primary btn-md font-weight-bolder" style="margin-right: 5px; margin-bottom: 5px; width: 100%"><i class="la la-search"></i>Search</a>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        @can('customer_wallet-create')
                            <a  href="{{ route('customer_wallets.create') }}" class="btn btn-primary btn-sm">
                                <i class="la la-plus"></i>Add new transaction
                            </a>
                        @endcan
                        <!-- <a  href="javascript:void(0)" id="div_show" class="btn btn-primary btn-sm ml-1">
                            <i class="la la-search"></i>
                        </a> -->
                    </div>
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <!-- <table id="myTable" class="table" style="width: 100%;" cellspacing="0"> -->
                            <table id="report_table" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%" >#</th>
                                        <th style="width:12%">Customer Name</th>
                                        <th style="width:8%">Phone#</th>
                                        <th style="width:8%">Credit</th>
                                        <th style="width:8%">Debit</th>
                                        <th style="width:10%">Reason</th>
                                        <th style="width:12%">Detail</th>
                                        <th style="width:12%">Date</th>
                                        <th style="width:8%">Month</th>
                                        <th style="width:12%">Time</th>
                                        <th width="10%" >Action</th>
                                    </tr>
                                </thead>
                              <tbody>
                              </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>

    <script>
        $(".search_div").hide();
        $(document).ready(function () { 
 
            // $('#div_show').click(function (e) {
            //     $(".search_div").slideToggle();
            // });
        
            // BEGIN::initialization of datatable
            var report_table = $('#report_table').DataTable({
                "aaSorting": [],
                "paging":   true,
                dom: 'Bfrtip',
                buttons: [
                     'csv'
                ],
                // "info":     false,
                "processing": true,
                "columnDefs": [ {
                "targets": [0,1,2,3,4,5,6,7,8,9,10],
                "orderable": false
                } ]
            });


            
        });

     

        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            get_list('/customer_wallet_list_onload/');
            // BEGIN:: re-schedule regular orders
            $('#get_report_btn').click(function (e) {
                e.preventDefault();
                var url  ='/customer_wallet_list/';
                get_list(url)

            
            });


            
            // END:: re-schedule regular orders
        });


        function get_list(url){
                $("#report_table > tbody").html("");
                var token = $("input[name='_token']").val();
                var cus_url = "{{ route('customer_wallets.index') }}" + url;
                $.ajax({
                    data: $('#form').serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        if(data.details){
                            // console.log(data.details);
                            var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                            rtable.rows.add($(data.details));
                            rtable.draw();
                        }else{
                            var txt = '';
                            var count = 0 ;
                            $.each(data.error, function() {
                                txt +=data.error[count++];
                                txt +='<br>';
                            });
                            toastr.error(txt);
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
    </script>

   
@endsection
