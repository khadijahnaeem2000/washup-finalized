@extends('layouts.master')
@section('title','Order')
@section('content')

@include( '../sweet_script')
    {!! Form::open(array('route' => 'order_packs.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
        {{  Form::hidden('created_by', Auth::user()->id ) }}

        <div class="row">
            <div class="col-lg-12">

                <div class="card card-custom card-collapsed" data-card="true" >
                        <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Details</h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                                <i class="ki ki-arrow-down icon-nm"></i>
                            </button>
                        </div>
                    </div>

                    <?php
                      

                        
                    ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                    <table class="table dt-responsive">
                                        <thead>
                                            <tr>
                                                <th width="40%">Time Slot</th>
                                                <th width="20%">Pick </th>
                                                <th width="20%">Drop / Pick & drop</th>
                                                <th width="20%">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php $tot = 0; ?>
                                            @foreach($time_slots as $key => $value)
                                                <tr>
                                                    <td>
                                                        {{$value}}
                                                    </td>
                                                    <td>
                                                        @foreach($pickup as $pkey => $pvalue)
                                                            @if($pkey == $key)
                                                                @if($pvalue>0)
                                                                    {{$pvalue}}
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                     
                                                    </td>
                                                    <td>
                                                        @foreach($dropoff as $dkey => $dvalue)
                                                            @if($dkey == $key)
                                                                @if($dvalue>0)
                                                                    {{$dvalue}}
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    
                                                    </td>
                                                    <td>
                                                        <?php $tot += ($pickup[$key] + $dropoff[$key]);?>
                                                        {{($pickup[$key] + $dropoff[$key])}}

                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align:right">
                                                    Total Location
                                                </th>
                                                <th> 
                                                    {{ $tot}}
                                                </th>
                                            </tr>

                                        </tfoot>
                                      
                                    </table><br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <!--begin::Card-->
                <div class="card card-custom">
                    <div class="card-header py-3">
                        <div class="card-title">
                            <h3 class="card-label">Delivery Schedule (Content)</h3>
                        </div>
                        @can('order_pack-create')
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                    <div class="input-icon" style="margin-right: 5px">
                                    {!! Form::select('status_id', $statuses,null, array('class' => 'form-control')) !!}
                                    </div><span></span>
                                    <div class="input-icon" style="margin-right: 5px">
                                        {!! Form::select('delivery_timeslot_id', $time_slots,null, array('class'=> 'form-control','autofocus' => '')) !!}
                                    </div><span></span>
                                    <div class="btn-group btn-group">
                                        <button type="submit" name ="btn_special" value='0'  class="btn btn-primary font-weight-bolder">Schedule <i class="fas fa-check"></i></button>
                                    </div>
                                <!--end::Button-->
                            </div>
                            <!-- <div class="input-icon" style="margin-left: 5px">
                                <a href="/assign_delivery_rider" class="btn btn-success font-weight-bolder">Assign Delivery Rider <i class="fas fa-check"></i></a>
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
                                        <th width="8%">Odr#</th>
                                        <th width="5%" >Ref#</th>
                                        <th width="15%">Name</th>
                                        <th width="10%">Contact#</th>
                                        <th width="15%" >Pickup Date</th>
                                        <th width="15%">Delivery Date</th>
                                        <th width="10%">Status</th>
                                        <th width="5%">Pack</th>
                                        <th width="5%">Type</th>
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
    {!! Form::close() !!}



<script>
    $(document).ready(function () {  
        var t = $('#myTable').DataTable({
            "aaSorting": [],
                "processing": true,
                "serverSide": false,
                
                "select":true,
                "ajax": "{{ url('order_packList') }}",
                "method": "GET",
                "columns": [
                    {"data": "checkbox",orderable:false,searchable:false},
                    {"data": "srno"},
                    {"data": "id"},
                    {"data": "ref_order_id"},
                    {"data": "name"},
                    {"data": "contact_no"},
                    {"data": "pickup_date"},
                    {"data": "delivery_date"},
                    {"data": "status_name"},
                    {"data": "pack_status"},
                    {"data": "order_type"},
                    {"data": "action",orderable:false,searchable:false}

                ]
            });
        t.on( 'order.dt search.dt', function () {
            t.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
        
    });
</script>
@endsection
