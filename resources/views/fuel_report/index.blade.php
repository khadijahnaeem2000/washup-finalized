@extends('layouts.master')
@section('title','Fuel Report')
@section('content')
    @include('../sweet_n_datatable_script')
    <style type="text/css">
        #report_table th, #report_table td {
            vertical-align: middle;
            text-align: center;
        }
        #report_table th {
            font-weight: bold;
        }
        .tRed {
            color: red;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .tGreen {
            color: green;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .tBlue {
            color: blue;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .detail-column {
            width: 150px; /* Adjust the width as needed */
        }
        .X{
            display:none;
        }
         #print-button {
            display: none; /* Initially hide the buttons */
        }
      @media print {
      #report_table th:nth-child(11), #report_table td:nth-child(11) {
        display: none;
      }
        .detail-column {
        display: none;
    }
      #card-gutter{
        display:none;
      }
      #div_show{
        display:none;
      }
      #print-button{
        display:none;
      }
    }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact search_div"id="card-gutter">
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
                                    <input type="date" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('to_date','To Date: ')) !!}
                                    <input type="date" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
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
                        <h3 class="card-label">Fuel Report</h3>
                    </div>
                    <div class="card-title">
                        <h3 class="card-labelX"id="X"></h3>
                    </div>
                    <div class="card-title">
                        <h3 class="card-labelName"></h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="javascript:void(0)" id="div_show" class="btn btn-primary btn-sm ">
                            <i class="la la-search"></i>
                        </a>
                        &nbsp;
                        <button id="print-button"class="btn btn-info btn-sm" onclick="printDiv()">
                            <i class="fa fa-print"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" id="main">
                    <div style="width: 100%;">
                        <div class="table-responsive"id="printableTable">
                            <table id="report_table" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Rider Name</th>
                                        <th>Opening</th>
                                        <th>Closing</th>
                                        <th>KMs</th>
                                        <th>Total</th>
                                        <th>Pick</th>
                                        <th>Drop</th>
                                        <th>Pick & Drop</th>
                                        <th class="detail-column">Detail</th>
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
            var report_table = $('#report_table').DataTable({
                "aaSorting": [],
                "paging": false,
                "searching": false,
                "info":false,
                dom: 'Bfrtip',
                buttons: [
                    'csv'
                ],
                "processing": false,
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                    "orderable": false
                }]
            });
        });

        function fetch_riders($hub_id){
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_riders') }}",
                method: 'POST',
                data: {hub_id: $hub_id, _token: token},
                success: function(data) {
                    if(data.data){
                        $("#rider_id").html(data.data);
                    } else {
                        $("#rider_id").html('<option>--- Select Rider ---</option>');
                    }
                }
            });
        }

        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#get_report_btn').click(function (e) {
                e.preventDefault();
                $("#report_table > tbody").html("");
                var token = $("input[name='_token']").val();
                var cus_url = "{{ route('fuel_report.index') }}" + '/fuel_list/';
                $.ajax({
                    data: $('#form').serialize(),
                    url: cus_url,
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.data){
                            $('.card-title h3.card-label').text( data.rider_name +'('+data.rider_incentive_name+')');
                             $('.card-title h3.card-labelX').text( data.rider_name +'('+data.rider_incentive_name+')').hide();
                             $('.card-title h3.card-labelName').text( data.from +' '+'to'+' '+data.to+'').hide();
                            var rtable = $('#report_table').DataTable();
                             $('#print-button').show();
                            rtable.clear().draw();
                            rtable.rows.add($(data.details));
                            rtable.draw();
                        } else {
                            var txt = '';
                            var count = 0;
                            var rtable = $('#report_table').DataTable();
                            $.each(data.error, function() {
                                txt += data.error[count++];
                                txt += '<br>';
                            });
                            toastr.error(txt);
                        }
                    },
                    error: function (data) {
                        var rtable = $('#report_table').DataTable();
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>
    <script>
        function toggleEdit(meter_id, rider_id, plan_date, id) {
            $.ajax({
                url: "{{ route('fuel_reports_check_lock') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    meter_id: meter_id,
                    rider: rider_id,
                    date: plan_date,
                    id: id
                },
                success: function(response) {
                    if (response.locked) {
                        toastr.error('The record is locked and cannot be edited.');
                    } else if(response.Nodata){
                         toastr.error('No reading found to edit');
                    }else {
                        window.location.href = 'fuel_report/' + id + '/edit';
                    }
                },
                error: function(xhr) {
                    toastr.error("An error occurred while checking the lock status.");
                }
            });
        }

        function toggleLock(meter_id) {
            $.ajax({
                url: "{{ route('fuel_reports_lock') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: meter_id
                },
                success: function(response) {
                    if (response.error) {
                        toastr.error(response.error);
                    } else {
                        toastr.success(response.success);
                        let btn = $('#lock-btn-' + meter_id);
                        if (response.status == 1) {
                            btn.removeClass('btn-secondary').addClass('btn-danger');
                        } else {
                            btn.removeClass('btn-danger').addClass('btn-secondary');
                        }
                    }
                },
                error: function(xhr) {
                    toastr.error("Only Admin can unlock");
                }
            });
        }
    </script>
<script>

function printDiv() {
   
    // Get the content of the element to print
    var printContents = document.getElementById('printableTable').innerHTML;
     var title = document.querySelector('.card-title h3.card-labelX').textContent;
      var date = document.querySelector('.card-title h3.card-labelName').textContent;
    // Remove unwanted elements from the content
    printContents = printContents.replace('<div class="dataTables_info">', ''); // Remove info message
    printContents = printContents.replace('<div class="dt-buttons">', ''); // Remove CSV button // Remove search bar
    printContents = printContents.replace(/<button.*?<\/button>/g, ''); // Remove buttons at the end

    // Prepare the HTML content for printing
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Fuel Report</title>');
    printWindow.document.write('<style type="text/css">');
    printWindow.document.write(`
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }
        table, th, td {
            border: 1px solid lightgray;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        .tRed {
            color: red;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .tGreen {
            color: green;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .tBlue {
            color: blue;
            background-color: #ebe6c7;
            font-weight: bold;
            padding: 2px;
            border-radius: 2px;
            display: block;
            margin: 2px;
        }
        .detail-column {
            display: none; /* Hide the detail column for printing */
        }
        #report_table th:nth-child(11), #report_table td:nth-child(11) {
            display: none;
        }
        
    `);
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
     printWindow.document.write('<center><h1>'+title+'</h1>');
      printWindow.document.write('<center><h3>'+date+'</h3>');
    printWindow.document.write('<table>');
    printWindow.document.write(printContents);
    printWindow.document.write('</table>');
    printWindow.document.write('</body></html>');

    // Close the document to apply the styles
    printWindow.document.close();

    // Wait for the content to be fully loaded before printing
    printWindow.onload = function () {
        printWindow.print();
        printWindow.close();
    };
}
</script>




@endsection
