@extends('layouts.master')
@section('title','Report')
@section('content')
    @include( '../sweet_n_datatable_script')
    <style type="text/css">
       #report_table th,#report_table td{
        vertical-align: middle;
            text-align: center;
        }
        #report_table th{
            font-weight:bold;
        }
        .tRed{
            color:red;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }
        .tGreen{
            color:green;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }
        .tBlue{
            color:blue;
            background-color:#ebe6c7;
            font-weight: bold;
            padding:2px;
            border-radius:2px;
            display:block;
            margin:2px;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact search_div">
                 <div class="card-header py-3">
                    {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data','style'=>'width:100%')) !!}
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('hub_id','Distribution Hub: ')) !!}
                                    {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id','onchange'=>'fetch_riders(this.value)')) !!}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('rider_id','Rider: ')) !!}
                                    {!! Form::select('rider_id',[''=>'--- Select ---'],0, array('class'=> 'form-control', 'id' =>'rider_id')) !!}
                                    @if ($errors->has('rider_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('rider_id')."</span>"!!} 
                                    @endif
                                    
                                </div>
                            </div>
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
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Report</h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="javascript:void(0)" id="div_show" class="btn btn-primary btn-sm ">
                            <i class="la la-search"></i>
                        </a>
                    </div>
                </div>
                    
                
                <div class="card-body">
                     <div style="width: 100%; ">
                        <div class="table-responsive">
                            <table id="report_table" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        
                                        <th colspan="3"></th>
                                        <th colspan="3"> Scheduled</th>
                                        <th colspan="3"> Actual</th>
                                        <th colspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Rider Name</th>
                                        <th>KMs</th>
                                        <th>Location</th>
                                        <th>Polybags</th>
                                        <th>KMs </th>
                                        <th>Location </th>
                                        <th>Polybags </th>
                                        <th>Receiving</th>
                                        <th>Detail</th>
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
 
            $('#div_show').click(function (e) {
                $(".search_div").slideToggle();
            });
            var hub_id = document.getElementById('hub_id').value;  
            fetch_riders(hub_id);
            // BEGIN::initialization of datatable
            var report_table = $('#report_table').DataTable({
                "aaSorting": [],
                "paging":   false,
                dom: 'Bfrtip',
                buttons: [
                     'csv'
                ],
                // "info":     false,
                "processing": false,
                "columnDefs": [ {
                "targets": [0,1,2,3,4,5,6,7,8,9,10],
                "orderable": false
                } ]
            });
        });

        function fetch_riders($hub_id){
            // alert($id);
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_riders') }}",
                method: 'POST',
                data: {hub_id:$hub_id, _token:token},
                success: function(data) {
                    // console.log(data);
                    if(data.data){
                        $("#rider_id").html(data.data);
                    }else{
                        $("#rider_id").html('<option>--- Select Rider ---</option>');
                    }
                }
            });
        }

        $(function () {
            // Ajax request setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // BEGIN:: re-schedule regular orders
            $('#get_report_btn').click(function (e) {
                e.preventDefault();
                $("#report_table > tbody").html("");
                var token = $("input[name='_token']").val();
                var cus_url = "{{ route('reports.index') }}" +'/report_list/';
                $.ajax({
                    data: $('#form').serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.data){
                            // console.log(data.details);
                            var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                            rtable.rows.add($(data.details));
                            rtable.draw();
                        }else{
                            var txt = '';
                            var count = 0 ;
                            var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                            $.each(data.error, function() {
                                txt +=data.error[count++];
                                txt +='<br>';
                            });
                            toastr.error(txt);
                        }
                    },
                    error: function (data) {
                        var rtable = $('#report_table').DataTable();
                            rtable.clear().draw();
                        console.log('Error:', data);
                    }
                });

            
            });
            // END:: re-schedule regular orders
        });
    </script>
@endsection
