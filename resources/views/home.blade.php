@extends('layouts.master')
    @section('content')
    
@include( '/sweet_script')
        <?php  $chk  = true; ?>
        <link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
        <script src="{{asset('libs/toastr/toastr.js')}}"></script>
        @can('admin_dashboard-list')
            <?php  $chk  = false; ?>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            
            <!--<div class="row justify-content-center" style="margin-bottom: 20px;">-->
            <!--    <div class="col-lg-12">-->
            <!--        {!! Form::open(array('id'=>'db_form','enctype'=>'multipart/form-data')) !!}-->
            <!--            <div class="card card-custom">-->
            <!--                <div class="card-header py-3">-->
            <!--                    <div class="card-title">-->
            <!--                        <h3 class="card-label">DB Dashboard</h3>-->
            <!--                    </div>-->
            <!--                    <div class="card-toolbar">-->
                                 

                                    
                                   
            <!--                        <h4 style="margin-right: 5px; margin-left: 5px"> Pickup Date: </h4>-->
            <!--                        <div style="margin-right: 5px">-->
            <!--                            <input type="date"  name = "pickup_date" id="pickup_date" class="form-control btn-sm" />-->
            <!--                        </div>-->
            <!--                        <h4 style="margin-right: 5px"> Delivery Date: </h4>-->
            <!--                        <div style="margin-right: 5px">-->
            <!--                            <input type="date" name = "delivery_date" id="delivery_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />-->
            <!--                        </div>-->

                                    
            <!--                        <a style ="margin-right: 5px; margin-left: 5px" class="btn btn-primary btn-sm font-weight-bolder" id ="btn_reverse" href="javascript:void(0)"> <i class="la la-refresh"></i>Reverse Orders</a>-->
            <!--                        <a class="btn btn-danger btn-sm font-weight-bolder" id ="btn_remove" href="javascript:void(0)"> <i class="la la-trash"></i>Remove all orders</a>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        {!! Form::close() !!}-->
            <!--    </div>-->
            <!--</div>-->


            <div class="row justify-content-center" style="margin-bottom: 20px;">
                <div class="col-lg-12">
                {!! Form::open(array('route' => 'home_dashboard.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                        <div class="card card-custom">
                            <!-- data-card="true" -->
                            <div class="card-header py-3">
                                <div class="card-title">
                                    <h3 class="card-label">Admin Dashboard</h3>
                                </div>
                                <div class="card-toolbar">
                                    <!--begin::Button-->
                                    
                                    
                                        <h4 style="margin-right: 5px">  Hub : </h4><span></span>
                                        <div style="margin-right: 5px">
                                            {!! Form::select('hub_id',$hubs, null, array('class' => 'form-control','required'=>'true','id'=>'hub_id','style'=>'width: 200px !important')) !!}
                                        </div>
                                        <h4 style="margin-right: 5px"> From: </h4>
                                        <div style="margin-right: 5px">
                                            <input type="date"  name = "from_date" id="from_date" class="form-control btn-sm" />
                                        </div>
                                        <h4 style="margin-right: 5px"> To: </h4>
                                        <div style="margin-right: 5px">
                                            <input type="date" name = "to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control btn-sm" />
                                        </div>
                                        <a class="btn btn-primary btn-sm font-weight-bolder" id ="plan_btn" href="javascript:void(0)"> <i class="la la-search"></i>Search</a>
                                 

                                        <div style="margin-left: 5px">
                                            <button class="btn btn-success btn-sm font-weight-bolder"> <i class="la la-pdf"></i>Export CSV</button>
                                        </div>
                                        <!--<div style="margin-left: 5px">-->
                                        <!--   <a  class="btn btn-secondary btn-sm font-weight-bolder" href="{{url('/exp_backup')}}" >Export Backup</a>-->
                                        <!--</div>-->
                                    
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <!--begin::Stats Widget 25-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::Body-->
                        <div class="card-body">	
                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Communication/Mail-opened.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="1"></path>
                                        <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id="new_customers">0</span>
                            <span class="font-weight-bold  font-size-sm">Total new customers</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 25-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 26-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::ody-->
                        <div class="card-body">	
                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Communication/Group.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id="new_orders">0</span>
                            <span class="font-weight-bold  font-size-sm">Total new customers' orders</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 26-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 27-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::Body-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-2x svg-icon-info">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Media/Equalizer.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <rect fill="#000000" opacity="0.3" x="13" y="4" width="3" height="16" rx="1.5"></rect>
                                        <rect fill="#000000" x="8" y="9" width="3" height="11" rx="1.5"></rect>
                                        <rect fill="#000000" x="18" y="11" width="3" height="9" rx="1.5"></rect>
                                        <rect fill="#000000" x="3" y="13" width="3" height="7" rx="1.5"></rect>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id="new_revenues">0</span>
                            <span class="font-weight-bold  font-size-sm"> New Orders Revenue </span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 27-->
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <!--begin::Stats Widget 25-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::Body-->
                        <div class="card-body">	
                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Communication/Mail-opened.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="0.3"></path>
                                        <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id="all_customers">0</span>
                            <span class="font-weight-bold  font-size-sm">Total  customers</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 25-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 26-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::ody-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Communication/Group.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id="all_orders">0</span>
                            <span class="font-weight-bold  font-size-sm">Total orders</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 26-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 27-->
                    <div class="card card-custom card-stretch gutter-b" style="background: #ffffff">
                        <!--begin::Body-->
                        <div class="card-body">	
                            <span class="svg-icon svg-icon-2x svg-icon-info">
                                    <!--begin::Svg Icon | path:/metronic/theme/html/demo7/dist/assets/media/svg/icons/Media/Equalizer.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"></rect>
                                            <rect fill="#000000" opacity="0.3" x="13" y="4" width="3" height="16" rx="1.5"></rect>
                                            <rect fill="#000000" x="8" y="9" width="3" height="11" rx="1.5"></rect>
                                            <rect fill="#000000" x="18" y="11" width="3" height="9" rx="1.5"></rect>
                                            <rect fill="#000000" x="3" y="13" width="3" height="7" rx="1.5"></rect>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block" id = "all_revenues">0</span>
                            <span class="font-weight-bold  font-size-sm">Total Orders Revenue </span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 27-->
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <!--begin::List Widget 7-->
                    <div class="card card-custom gutter-b card-stretch">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">Customers</h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-0">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center flex-wrap mb-10">
                                <!--begin::Text-->
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Total Customers</a>
                                    <span class=" font-weight-bold"> </span>
                                </div>
                                <!--end::Text-->	
                                <span class="label label-xl label-light label-inline my-lg-0 my-2 text-dark-50 font-weight-bolder" id="s_all_customers"> 0</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center flex-wrap mb-10">
                                <!--begin::Text-->
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">New Customers</a>
                                </div>
                                <!--end::Text-->	
                                <span class="label label-xl label-light label-inline my-lg-0 my-2 text-dark-50 font-weight-bolder" id="s_new_customers">0</span>
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="d-flex align-items-center flex-wrap mb-10">
                                <!--begin::Text-->
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Active Customers</a>
                                </div>
                                <!--end::Text-->	
                                <span class="label label-xl label-light label-inline my-lg-0 my-2 text-dark-50 font-weight-bolder" id="s_active_customers">0</span>
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="d-flex align-items-center flex-wrap mb-10">
                                <!--begin::Text-->
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Pending Orders</a>
                                </div>
                                <!--end::Text-->	
                                <span class="label label-xl label-light label-inline my-lg-0 my-2 text-dark-50 font-weight-bolder" id="pending_orders">0</span>
                            </div>
                            <!--end::Item-->
                            
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::List Widget 7-->
                </div>
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Order Chart</h3>
                            </div>
                        </div>
                        <div class="card-body" style="position: relative;">
                            <div id="chart" style="max-width: 650px; margin: 35px auto;">
                            </div>                        
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>

            <script>
                $('#plan_btn').click(function (e) {
                    e.preventDefault();
                    get_data();
                    
                });
                

                function draw_chart(months,tot_orders,cncl_orders,comp_orders){
                    $("#chart").empty();
                    // console.log(cncl_orders);
                    var options = {
                        series: 
                            [
                                { 
                                    name: 'Completed Orders',
                                    data: comp_orders
                                },
                                {
                                    name: 'Total Orders',
                                    data: tot_orders
                                }, 
                                {
                                    name: 'Canceled Orders',
                                    data: cncl_orders
                                }
                            ],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: months,
                        },
                        yaxis: {
                            title: {
                                text: 'Orders'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                return val + " orders"
                                }
                            }
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                }

                function get_data(){
                    
                    // $("#myTable > tbody").html("");
                    // var wash_house_id           = document.getElementById('wash_house_id').value; 
                    var token                   = $("input[name='_token']").val();
                    var cus_url                 = 'fetch_dashboard';
                    $.ajax({
                        data: $('#form').serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data.all_orders){
                                $('#new_orders').html(data.new_orders);
                                $('#new_customers').html(data.new_customers);
                                $('#all_customers').html(data.all_customers);

                                $('#all_orders').html(data.all_orders);
                                $('#all_revenues').html(data.all_revenues);
                                $('#new_revenues').html(data.new_revenues);

                                $('#s_new_customers').html(data.new_customers);
                                $('#s_all_customers').html(data.all_customers);

                                $('#pending_orders').html(data.pending_orders);
                                $('#s_active_customers').html(data.active_customers);

                                // draw_chart(data.months);

                                draw_chart(data.months,data.tot_orders,data.cncl_orders,data.comp_orders)


                            }else{
                                var txt = '';
                                var count = 0 ;
                                $.each(data.error, function() {
                                    txt +=data.error[count++];
                                    txt +='<br>';
                                });
                                toastr.error(txt);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }

                $('#btn_remove').click(function (e) {
                    e.preventDefault();
                    console.log("remove");
                    var token                   = $("input[name='_token']").val();
                    var cus_url                 = 'remove_orders';
                    $.ajax({
                        data: $('#db_form').serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data){
                                toastr.success("all orders removed");
                            }else{
                                var txt = '';
                                var count = 0 ;
                                $.each(data.error, function() {
                                    txt +=data.error[count++];
                                    txt +='<br>';
                                });
                                toastr.error(txt);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                    
                });

                $('#btn_reverse').click(function (e) {
                    e.preventDefault();
                    console.log("btn_reverse");
                    // get_data();
                    
                    var token                   = $("input[name='_token']").val();
                    var cus_url                 = 'reverse_orders';
                    $.ajax({
                        data: $('#db_form').serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data){
                                toastr.success("all orders reversed");
                            }else{
                                var txt = '';
                                var count = 0 ;
                                $.each(data.error, function() {
                                    txt +=data.error[count++];
                                    txt +='<br>';
                                });
                                toastr.error(txt);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                    
                });


                $('#export_btn').click(function (e) {
                    e.preventDefault();
                    console.log("export_btn");
                    // get_data();
                    
                    var token                   = $("input[name='_token']").val();
                    var cus_url                 = 'export_csv';
                    $.ajax({
                        data: $('#form').serialize(),
                        url: cus_url,
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if(data){
                                toastr.success("all orders reversed");
                            }else{
                                var txt = '';
                                var count = 0 ;
                                $.each(data.error, function() {
                                    txt +=data.error[count++];
                                    txt +='<br>';
                                });
                                toastr.error(txt);
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                    
                });
            </script>
        @endcan

        <?php   if ($chk){?>
            <div class="row justify-content-center" style="margin-bottom: 20px;">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <!-- data-card="true" -->
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Sorry! you don't have to view the dashboard</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    @endsection
