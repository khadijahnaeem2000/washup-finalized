@extends('layouts.master')
@section('title','Complaint')
@section('content')
    @include( '../sweet_script')
    <style>
        .cntr{
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 400px; 
            max-height: 264px;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Show @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="{{ route('complaints.index') }}" class="btn btn-primary btn-sm font-weight-bolder">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td width="50%">Customer Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer Contact#</td>
                                        <td>{{$data->contact_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Order No</td>
                                        <td>{{$data->order_id}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickup Date</td>
                                        <td>{{$data->pickup_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Date</td>
                                        <td>{{$data->delivery_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Complaint Date</td>
                                        <td>{{$data->complaint_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Complaint Nature</td>
                                        <td>{{$data->nature_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Complaint Tag</td>
                                        <td>{{$data->tag_name}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Complaint Status</td>
                                        <td>{{$data->complaint_status}}</td>
                                    </tr>

                                    <tr>
                                        <td>Iron rating</td>
                                        <td>{{$data->iron_rating}}</td>
                                    </tr>

                                    <tr>
                                        <td>Softner rating</td>
                                        <td>{{$data->softner_rating}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Picture  </td>
                                        <td>
                                            @if($data->image)
                                                <?php $images =  explode('|',$data->image);?>
                                                @foreach ($images as $key => $value) 
                                                    <img src="{{ asset('uploads/complaints/'.$value) }}" id = "{{$value}}"  class="users-avatar-shadow rounded-circle img_pre" style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); margin-right:10px;margin-bottom:10px" height="64" width="64">
                                                @endforeach
                                            @else
                                                <img src="{{ asset('uploads/no_image.png') }}" alt="users view avatar" class="users-avatar-shadow rounded-circle"  style= " box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"height="64" width="64">
                                           @endif
                                        </td>
                                    </tr>
                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Image preview</h4>
                </div>
                <div class="modal-body">
                    <img src="" id="imagepreview" class ="cntr"  >
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(".img_pre").on("click", function() {
        $('#imagepreview').attr('src',$(this).attr("src")); // here asign the image to the modal when the user click the enlarge link
        $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
    });
    </script>
@endsection
