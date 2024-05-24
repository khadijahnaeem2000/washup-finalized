
@extends('layouts.master')
@section('title','Delivery charges')
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
                     @can('delivery_charge-create')
                        <!-- <div class="card-toolbar">
                            <a  href="{{ route('delivery_charges.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new delivery charge</a>
                        </div> -->
                    @endcan
                </div>
                <div class="card-body">
                    <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                    <th width="2%" >#</th>
                                    <th>Order Amount</th>
                                    <th>Delivery Charge</th>
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
    <br>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage VAT</h3>
                    </div>
                     @can('delivery_charge-create')
                        <!-- <div class="card-toolbar">
                            <a  href="{{ route('delivery_charges.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new delivery charge</a>
                        </div> -->
                    @endcan
                </div>
                <div class="card-body">
                    <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="vatTable" class="table" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                    <th width="2%" >#</th>
                                    <th>VAT</th>
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
                "select":true,
                "ajax": "{{ url('delivery_charge_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "order_amount"},
                    {"data": "delivery_charges"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });

            $('#vatTable').DataTable({
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "select":true,
                "ajax": "{{ url('vat_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "srno"},
                    {"data": "vat"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
        });
    </script>
@endsection
