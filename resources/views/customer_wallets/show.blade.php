@extends('layouts.master')
@section('title','Wallet')
@section('content')
   <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Show @yield('title')  - Current Balance: <b>{{$current_bal}}</b></h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="{{ route('customer_wallets.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td>Customer name</td>
                                        <td>{{$data->customer_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer #</td>
                                        <td>{{$data->contact_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Voucher/ Credit amount</td>
                                        <td>{{$data->in_amount}}</td>
                                    </tr>

                                    <tr>
                                        <td>Debit amount</td>
                                        <td>
                                            @if(isset($data->out_amount))
                                                {{$data->out_amount}}
                                            @else
                                                {{ 0 }}
                                            @endif
                                            
                                        </td>
                                    </tr>

                                    <!-- <tr>
                                        <td>Voucher Reason</td>
                                        <td>{{$data->reason_name}}</td>
                                    </tr> -->
                                    <tr>
                                        <td>Detail</td>
                                        <td>{{$data->detail}}</td>
                                    </tr>
                                    
                                    @if(isset($data->rider_name))
                                        <tr>
                                            <td>Rider</td>
                                            <td>
                                                @if(isset($data->rider_name))
                                                    {{$data->rider_name}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                
                                    <tr>
                                        <td>Created at</td>
                                        <td>{{$data->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td>Update at</td>
                                        <td>{{$data->updated_at}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
           <br>
        </div>
    </div>
@endsection
