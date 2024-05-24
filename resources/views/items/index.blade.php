@extends('layouts.master')
@section('title','Items')
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
                    <div class="card-toolbar">
                        <a href="{{ route('items.create') }}" class="btn btn-primary font-weight-bolder"> 
                            <i class="la la-plus"></i>Add new item
                            </a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%">#</th>
                                        <th>Item Name</th>
                                        <th>Short Name</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
            var t = $('#myTable').DataTable({
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "select":false,
                "ajax": "{{ url('item_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "DT_RowIndex"},
                    {"data": "name"},
                    {"data": "short_name"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
          
        
        
        });
    </script>
@endsection
