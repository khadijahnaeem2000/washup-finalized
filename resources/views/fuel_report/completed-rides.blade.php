@extends('layouts.master')
@section('title','Wallet')
@section('content')
    @include( '../sweet_n_datatable_script')
    <style>
       #report_table th,#report_table td{
        vertical-align: middle;
            text-align: center;
        }
        #report_table th{
            font-weight:bold;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
  
       
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                            <a  href="{{ route('customer_wallets.create') }}" class="btn btn-primary btn-sm">
                                <i class="la la-plus"></i>Add new transaction
                            </a>
                    
                        <!-- <a  href="javascript:void(0)" id="div_show" class="btn btn-primary btn-sm ml-1">
                            <i class="la la-search"></i>
                        </a> -->
                    </div>
                </div>
                <div class="card-body">
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <!-- <table id="myTable" class="table" style="width: 100%;" cellspacing="0"> -->
                            <table id="report_table" class="table table-bordered dt-responsive" style="width: 100%;" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="2%" >#</th>
                                        <th style="width:12%">Customer Name</th>
                                        <th style="width:8%">Phone#</th>
                                        <th style="width:8%">Credit</th>
                                        <th style="width:8%">Debit</th>
                                        <th style="width:10%">Reason</th>
                                        <th style="width:12%">Detail</th>
                                        <th style="width:12%">Date</th>
                                        <th style="width:8%">Month</th>
                                        <th style="width:12%">Time</th>
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


   
@endsection
