@extends('layouts.master')
@section('title','Order')
@section('content')
    @include( '../sweet_script')
    <style>
         .checkbox-label {
        visibility: hidden;
        }
    

   </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Verify Order (Content)</h3>
                    </div>
                    <div class="card-toolbar">
                        <div style="margin-right: 10px">
                            {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id')) !!}
                        </div>
                        @can('waive')
                        <div style="margin-right: 5px">
                           <a href="" class="btn btn-primary">Waive Delivery</a>
                        </div>
                        @endcan
                    </div>
                </div>
                <?php $checkvar = false; ?>
                    @can('special_tag-print')
                        <?php $checkvar = true; ?>
                    @endcan
                <?php 
                    if(!$checkvar){?>
                        <style>
                            .chk_prm{
                                display:none;
                            }
                        </style>
                <?php } ?>
                
                <div class="card-body">
                <h4 id="note" style="color:red; font-weight:bold; text-align:center"></h4>
                     <div style="width: 100%; padding-left: -10px; ">
                        <div class="table-responsive">
                            <table id="myTable" class="table" style="width: 100%;" cellspacing="0">
                              <thead>
                                <tr>
                                       <th width="2%" >
                                            <!-- <input hidden name="checkAll" class="checkAll"  type="checkbox" /> -->
                                                <!-- <div class="checkbox-inline">
                                                    <label class="checkbox checkbox-success">
                                                        <input name="checkAll" class="checkAll"  type="checkbox" />
                                                        <span></span> 
                                                    </label>
                                                </div> -->
                                                <!-- <button name="checkAll" id="checkAll" class="btn btn-success btn-xs"> -->
                                                    <i class="fas fa-check" id="selectAll" class="btn btn-success btn-xs"></i>
                                                <!-- </button> -->
                                            </th>
                                    <th width="2%" >#</th>
                                    <th width="5%" >Order#</th>
                                    <th>Name</th>
                                    <th>Contact#</th>
                                    <th>Pickup Date</th>
                                    <th>Status</th>
                                    <th>Wash House</th>
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
    var hub_id = $('#hub_id').val();
    var order_table;

    if (hub_id > 0) {
        order_table = $('#myTable').DataTable({
            "aaSorting": [],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ url('order_verify_list') }}/" + hub_id,
                "method": "GET"
            },
            "columns": [
                {"data": "checkbox", orderable:false, searchable:false},
                {"data": "srno"},
                {"data": "id"},
                {"data": "name"},
                {"data": "contact_no"},
                {"data": "pickup_date"},
                {"data": "status_name"},
                {"data": "wash_house_name"},
                {"data": "action", orderable: false, searchable: false}
            ]
        });

        $('#hub_id').change(function () {
            var hub_id = $(this).val();
            order_table.ajax.url("{{ url('order_verify_list') }}/" + hub_id).load();
        });

        $('#selectAll').click(function (event) {
            event.stopPropagation();
            $('input:checkbox').prop('checked', !$(this).prop('checked'));
        });

        $('.btn-primary').click(function (event) {
            event.preventDefault();
            var selectedIDs = [];
           $('.checkbox-inline input:checkbox:checked').each(function () {
        selectedIDs.push($(this).attr('name').split('[')[1].split(']')[0]);
    });
        
               
            if (selectedIDs.length > 0) {
                $.ajax({
                    url: "{{ url('waver_delivery_request') }}",
                    type: "POST",
                    data: {
                        ids: selectedIDs,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data.success) {
                            toastr.success("Successfully updated order to wavier delivery");
                            setTimeout(function () {
                                location.reload();
                            }, 4000);
                        } else {
                            console.log("Error: " + data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                alert('Please select at least one order to waver delivery.');
            }
        });
    } else {
        $('#note').text('!!!! logged in user does not have any distribution hub !!!!');
        $('#hub_id').append('<option disabled>--- No Hub ---</option>');
    }
});

</script>

@endsection
