@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_script')
    {!! Form::open(array('method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
        {{  Form::hidden('created_by', Auth::user()->id ) }}
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Manage Wash-house @yield('title')</h3>
                        </div>
                        @can('Wash_house_order-list')
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <div style="margin-right: 10px">
                                <h4 style="text-align:center">  Distribution Hub </h4>
                                {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','autofocus' => '','required'=>'true','id'=>'hub_id')) !!}
                            </div>
                            <div style="margin-right: 10px">
                                <h4 style="text-align:center">  Wash-house </h4>
                                {!! Form::select('wash_house_id', [0=>'--- No Wash house ---'],null, array('class' => 'form-control','required'=>'true','id'=>'wash_house_id')) !!}
                            </div>
                            <div style="margin-right: 10px">
                                <h4 style="text-align:center">  Actions </h4>
                                <div class="btn-group btn-group">
                                    @can('special_wash_house-assign')
                                        <button type="" name ="btn_assign_special" id="btn_assign_special" class="btn btn-success font-weight-bolder btn_special">Assign Special<i class="fas fa-check"></i></button>
                                    @endcan
                                    <button type="" name ="btn_assign" id="btn_assign"  class="btn btn-primary font-weight-bolder btn_special">Assign <i class="fas fa-check"></i></button>
                                </div>
                            </div> <input type ="hidden" name="special">
                            <!--end::Button-->
                        </div>
                            <!-- <div class="card-toolbar">
                                <a  href="/update_status" class="btn btn-primary font-weight-bolder">
                                    Move to hub <i class="fas fa-angle-double-right"></i>
                                </a>
                            </div> -->
                        @endcan
                    </div>
                    <div class="card-body">
                        <div style="width: 100%; padding-left: -10px; ">
                            <div class="table-responsive">
                                <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="2%" ></th>
                                            <th width="2%" >#</th>
                                            <th>Order#</th>
                                            <th>Customer Name</th>
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
                    "ajax": "{{ url('wash_house_order_list') }}" +'/'+hub_id,
                    "method": "GET",
                    "columns": [
                        {"data": "checkbox",orderable:false,searchable:false},
                        {"data": "srno"},
                        {"data": "id"},
                        {"data": "name"},
                        {"data": "status_name"},
                        {"data": "wash_house_name"},
                        {"data": "summary_printed"}
                    ]
                });
                // BEGIN:: Btn Assign Special
                $('#btn_assign_special').click(function (e) {
                    $("input[name='special']").val(1);
                    e.preventDefault();
                    $.ajax({
                        data: $('#form').serialize(),
                        url: "{{ route('wash_house_orders.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data.success){
                                // toastr.success(data.success);
                                var hub_id          = document.getElementById('hub_id').value;  
                                                        fetch_wash_house(hub_id);
                                order_table.ajax.url( 'wash_house_order_list/'+hub_id ).load(); 
                                // toastr.success(data.success);
                                const swalWithBootstrapButtons = Swal.mixin({
                                    customClass: {
                                    confirmButton: 'btn btn-success',
                                    // cancelButton: 'btn btn-danger'
                                    },
                                    buttonsStyling: false
                                })

                                swalWithBootstrapButtons.fire({
                                    title: 'Are you sure?',
                                    text: (data.success)+ " Have you put the order(s) in corret pile?",
                                    icon: 'success',
                                    // showCancelButton: true,
                                    confirmButtonText: 'Yes!',
                                    // cancelButtonText: 'No, cancel!',
                                    reverseButtons: true
                                }) 

                            }else{
                                toastr.error(data.error);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                });
                // END:: Btn Assign Special

                // BEGIN:: Btn Assign 
                $('#btn_assign').click(function (e) {
                    $("input[name='special']").val(0);
                    e.preventDefault();
                    $.ajax({
                        data: $('#form').serialize(),
                        url: "{{ route('wash_house_orders.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data.success){
                                // toastr.success(data.success);
                                var hub_id          = document.getElementById('hub_id').value;  
                                                        fetch_wash_house(hub_id);
                                order_table.ajax.url( 'wash_house_order_list/'+hub_id ).load(); 
                                // toastr.success(data.success);
                                const swalWithBootstrapButtons = Swal.mixin({
                                    customClass: {
                                    confirmButton: 'btn btn-success',
                                    // cancelButton: 'btn btn-danger'
                                    },
                                    buttonsStyling: false
                                })

                                swalWithBootstrapButtons.fire({
                                    title: 'Are you sure?',
                                    text: (data.success)+ " Have you put the order(s) in corret pile?",
                                    icon: 'success',
                                    // showCancelButton: true,
                                    confirmButtonText: 'Yes!',
                                    // cancelButtonText: 'No, cancel!',
                                    reverseButtons: true
                                }) 

                            }else{
                                toastr.error(data.error);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                });
                // END:: Btn Assign 
                
                // END:: schedule payment orders
                $('#hub_id').change(function () {
                    var hub_id          = document.getElementById('hub_id').value;  
                                          fetch_wash_house(hub_id);
                    order_table.ajax.url( 'wash_house_order_list/'+hub_id ).load();
                });


            }else{
                $('#hub_id').append('<option value = "0">--- No Hub ---</option>');
                $('#wash_house_id').append('<option value = "0">--- No Wash house ---</option>');
            }
        });
    </script>
@endsection
