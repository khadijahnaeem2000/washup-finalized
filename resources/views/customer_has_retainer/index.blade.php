@extends('layouts.master')
@section('title','Retainer Days')
@section('content')
    @include( '../sweet_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label"> @yield('title')</h3>
                    </div>
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                    <th width="2%" >#</th>
                                    <th>Name</th>
                                    <th>Contact#</th>
                                    <th>Type</th>
                                    <th>Day</th>
                                    <th>Timeslot</th>
                                    <th>Action</th>
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
                "select":true,
                "ajax": "{{ url('retainer_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "customer_name"},
                    {"data": "contact_no"},
                    {"data": "customer_type"},
                    {"data": "day_name"},
                    {"data": "timeslot_name"},
                    {"data": "note",orderable:false}
                ]
            });
        });
    </script>
@endsection
