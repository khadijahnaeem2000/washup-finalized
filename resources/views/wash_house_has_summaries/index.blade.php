@extends('layouts.master')
@section('title','Summary')
@section('content')
    @include( '../sweet_script')
    {!! Form::open(array('route' => 'wash_house_summaries.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
        {{  Form::hidden('created_by', Auth::user()->id ) }}
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Manage Wash-house @yield('title')</h3>
                        </div>
                        @can('Wash_house_summary-list')
                            <div class="card-toolbar">
                                <div style="margin-right: 10px">
                                    <h4 style="text-align:center"> Pickup Date </h4>
                                    {!! Form::date('pickup_date', date('Y-m-d'), array('class' => 'form-control')) !!}
                                </div>
                                <div style="margin-right: 10px">
                                    <h4 style="text-align:center">  Distribution Hub </h4>
                                    {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','autofocus' => '','required'=>'true','id'=>'hub_id')) !!}
                                </div>
                                <div style="margin-right: 10px">
                                    <h4 style="text-align:center">  Wash-house </h4>
                                    {!! Form::select('wash_house_id', [0=>'--- No Wash house ---'],null, array('class' => 'form-control','required'=>'true','id'=>'wash_house_id')) !!}
                                </div>
                                <div style="margin-right: 10px">
                                    <h4 style="text-align:center">  Print </h4>
                                    <div class="btn-group btn-group">
                                        <button type="submit" class="btn btn-primary font-weight-bolder"><i class="la la-print"></i></button>
                                    </div>
                                </div> 
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="2%" >#</th>
                                            <th>Order#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Washhouse</th>
                                            <th>Summary Printed</th>
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
    {!! Form::close() !!}
    <script>
        function fetch_wash_house($hub_id){
            var token           = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_wash_house') }}",
                method: 'POST',
                data: {hub_id:$hub_id, _token:token},
                success: function(data) {
                    if(data.data){
                        $("#wash_house_id").empty().html(data.data);
                    }else{
                        $("#wash_house_id").empty().html('<option value = "0">--- No Wash house ---</option>');
                    }
                }
            });
        }
      
        $(document).ready(function () {
           
            var hub_id          = document.getElementById('hub_id').value;  
            if(hub_id > 0){
                // fetching list of wash-house which are belongs to the selected distribution hub
                fetch_wash_house(hub_id);
                var order_table =  $('#myTable').DataTable({
                    "aaSorting": [],
                    "processing": true,
                    "serverSide": true,
                    "ajax": "{{ url('wash_house_summary_list') }}" +'/'+hub_id,
                    "method": "GET",
                    "columns": [
                        {"data": "srno"},
                        {"data": "id"},
                        {"data": "name"},
                        {"data": "status_name"},
                        {"data": "wash_house_name"},
                        {"data": "summary_printed"}
                    ]
                });
              
                
                $('#hub_id').change(function () {
                    var hub_id          = document.getElementById('hub_id').value;  
                                          fetch_wash_house(hub_id);
                    order_table.ajax.url( 'wash_house_summary_list/'+hub_id ).load();
                });


            }else{
                $('#hub_id').append('<option value = "0">--- No Hub ---</option>');
                $('#wash_house_id').append('<option value = "0">--- No Wash house ---</option>');
            }
        });
    </script>

@endsection
