@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Wash-House Dashboard</h3>
                       
                    </div>
                    <div class="card-toolbar">
                        <div style="margin-right: 5px">
                        {!! Form::open(array('id'=>'form','enctype'=>'multipart/form-data','style'=>'width:100%')) !!}
                            {!! Form::select('wash_house_id',$wash_houses, null, array('class' => 'form-control','required'=>'true','id'=>'wash_house_id')) !!}
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <h4 id="note" style="color:red; font-weight:bold; text-align:center"></h4>
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order#</th>
                                        <th>Name</th>
                                        <th>Pickup Date</th>
                                        <th>Delivery Date</th>
                                        <th>Addon</th>
                                        <th>Comment</th>
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
        $('#wash_house_id').change(function (e) {
            e.preventDefault();
            get_data();
        });

        function get_data(){
            $("#myTable > tbody").html("");
            var wash_house_id           = document.getElementById('wash_house_id').value; 
            var token                   = $("input[name='_token']").val();
            var cus_url                 = "{{ route('wh_dashboards.index') }}" +'/wh_order_list/';
            $.ajax({
                data: $('#form').serialize(),
                url: cus_url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.data){
                        var rtable = $('#myTable').DataTable();
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
               
        $(document).ready(function () {
            var wash_house_id           = document.getElementById('wash_house_id').value;  
            if(wash_house_id > 0){
                var myTable = $('#myTable').DataTable({
                    "aaSorting": [],
                    "paging":   true,
                    "info":     true,
                    "processing": true,
                    "columnDefs": [ {
                    "targets": [6],
                    "orderable": false
                    } ]
                });
                get_data();
            }else{
                $('#wash_house_id').append('<option disabled>--- No Wash-house ---</option>');
            }
        });
    </script>
@endsection
