@extends('layouts.master')
@section('title','Service Rate List')
@section('content')
@include( '../sweet_script')

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Replicate @yield('title')</h3>
                   
                </div>
                <!--begin::Form-->
                {!! Form::open(array('url' => 'rep_service_rate_list','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                
                    <div class="card-body">
                    
                        <div class="row">
                            <span class='span_danger' style="display: block;text-align:center; margin-bottom:10px">( Replicate rate list of one wash-house to another wash-house )</span>
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Form::select('wash_house_id_1',["0"=>"-- Select Wash-house -- "]+$wash_houses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('wash_house_id_1'))  
                                        {!! "<span class='span_danger'>". $errors->first('wash_house_id_1')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Form::select('wash_house_id_2',["0"=>"-- Select Wash-house -- "]+$wash_houses,null, array('class' => 'form-control')) !!}
                                    @if ($errors->has('wash_house_id_2'))  
                                        {!! "<span class='span_danger'>". $errors->first('wash_house_id_2')."</span>"!!} 
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary mr-2 btn-block" >Replicate</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Manage @yield('title')</h3>
                    </div>
                     @can('rate_list-create')
                        <div class="card-toolbar">
                            <a  href="{{ route('rate_lists.create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="la la-plus"></i>Add new rate list</a>
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
                                        <th>Item</th>
                                        <th>Service </th>
                                        <th>Wash-house</th>
                                        <th>Rate</th>
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
            var t = $('#myTable').DataTable({
            "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "select":true,
                "ajax": "{{ url('rate_list_list') }}",
                "method": "GET",
                "columns": [
                    {"data": "DT_RowIndex"},
                    {"data": "item_name"},
                    {"data": "service_name"},
                    {"data": "wash_house_name"},
                    {"data": "rate"},
                    {"data": "action",orderable:false,searchable:false}
                ]
            });
        });
    </script>
@endsection
