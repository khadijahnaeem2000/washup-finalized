
@extends('layouts.master')
@section('title','Service')
@section('content')
<style>
    .tRed{
        color:red;
        background-color:#ebe6c7;
        font-weight: bold;
        padding:2px;
        border-radius:2px;
    }
    .tGreen{
        color:green;
        background-color:#ebe6c7;
        font-weight: bold;
        padding:2px;
        border-radius:2px;
    

    }
    .imgStyle{
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        width: 64px;
        height: 64px;
        background: #3495eb;
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
                        <a  href="{{ route('services.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td>@yield('title') Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Hanging Capability </td>
                                        <?php if( $data->hanger == 1 ){?>
                                            <td><span class = "tGreen"> Yes </span></td>
                                        <?php }else{ ?>
                                            <td><span class = "tRed">No </span></td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td>Rate</td>
                                        <td>{{$data->rate}}</td>
                                    </tr>
                                    <tr>
                                        <td>Unit</td>
                                        <td>{{$data->unit_name}}</td>
                                    </tr>   
                                    <tr>
                                        <td>Quantity</td>
                                        <td>{{$data->qty}}</td>
                                    </tr>

                                    <tr>
                                        <td>Description</td>
                                        <td>{{$data->description}}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Number</td>
                                        <td>{{$data->order_number}}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Pic (Mobile)  </td>
                                        <td >
                                            @if($data->image)
                                                <img src="{{ asset('uploads/services/'.$data->image) }}" class="rounded-circle imgStyle"  >
                                            @else
                                                <img src="{{ asset('uploads/no_image.png') }}" class="rounded-circle imgStyle" >
                                           @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Service Pic (Web Panel) </td>
                                        <td >
                                            @if($data->web_image)
                                                <img src="{{ asset('uploads/services/'.$data->web_image) }}" class="rounded-circle imgStyle" >
                                            @else
                                                <img src="{{ asset('uploads/no_image.png') }}" class="rounded-circle imgStyle" >
                                           @endif
                                        </td>
                                    </tr>

                                    
                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Item Details</h3>
                    </div>
                    <div class="card-toolbar">
                       
                    </div>
                </div>
              
                <!-- Showing Addons -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <thead>
                                    
                                        <tr>
                                            <th width="40%">Item</th>
                                            <th width="40%">Addon</th>
                                            <th width="10%">Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selected_items as $key => $value)
                                            <tr>
                                                <td>{{$value->item_name }}</td>
                                                <td>
                                                    @foreach($selected_addons as $addkey => $addvalue)
                                                        @if($addvalue->item_id == $value->id)
                                                            <label class="badge badge-success">{{$addvalue->addon_name }}</label>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                
                                                <td>{{$value->item_rate }}</td>
                                            </tr>
                                        @endforeach
                                    <tbody>
                                   
                                  
                                </table><br><br>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
@endsection
