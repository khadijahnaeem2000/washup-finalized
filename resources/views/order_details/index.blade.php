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
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                     @can('order_detail-create')
                        <div class="card-toolbar">
                            <a  href="/fn_move_to_hub" class="btn btn-primary font-weight-bolder">
                                Move to hub <i class="fas fa-angle-double-right"></i>
                            </a>
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
                                        <th>Contact#</th>
                                        <th>Pickup Date</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th>Type</th>
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
        $(document).ready(function () {  
            $('#myTable').DataTable({
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ url('order_detail_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "contact_no"},
                    {"data": "pickup_date"},
                    {"data": "delivery_date"},
                    {"data": "status_name"},
                    {"data": "order_type"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
        });
    </script>
@endsection
