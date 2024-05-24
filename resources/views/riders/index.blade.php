@extends('layouts.master')
@section('title','Riders')
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
                     @can('rider-create')
                        <div class="card-toolbar">
                            <a  href="{{ route('riders.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new Rider</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th>Name</th>
                                    <th>Contact#</th>
                                    <th>Mx Loc</th>
                                    <th>Mx Route</th>
                                    <th>Mx Pick</th>
                                    <th>Mx Drop Weight</th>
                                    <th>Mx Drop Size</th>
                                    <th>Vehicle</th>
                                    <th>P.Zone</th>
                                    <th>Rider Compensation</th>
                                    <th>Status</th>
                                    <th>Forgot</th>
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
                "aaSorting" : [],
                "processing": true,
                "serverSide": true,
                "ajax"      : "{{ url('rider_list') }}",
                "method"    : "GET",
                "columns"   : [
                    {"data": "DT_RowIndex"},
                    {"data": "name"},
                    {"data": "contact_no"},
                    {"data": "max_loc"},
                    {"data": "max_route"},
                    {"data": "max_pick"},
                    {"data": "max_drop_weight"},
                    {"data": "max_drop_size"},
                    {"data": "vehicle_type_name"},
                    {"data": "zone_name"},
                    {"data": "rider_incentives_name"},
                    {"data": "status"},
                    {"data": "forget"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
        });
    </script>
@endsection
