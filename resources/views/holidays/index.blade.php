
@extends('layouts.master')
@section('title','Holidays')
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
                     @can('holiday-create')
                        <div class="card-toolbar">
                            <a  href="{{ route('holidays.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new holiday</a>
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
                                        <th>Name</th>
                                        <th>Holiday Date</th>
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
                "ajax": "{{ url('holiday_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "name"},
                    {"data": "holiday_date"},
                    {"data": "action",orderable:false,searchable:false}

                ]
            });
        });
    </script>
@endsection
