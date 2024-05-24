<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="description" content="Updates and statistics" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Washup') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/media/logo-main.png') }}" />
<!-- 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link href="{{ asset('libs/font_family.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/font_family_poppins.css') }}" rel="stylesheet">

        <!--end::Fonts-->
        <!--begin::Page Vendors Styles(used by this page)-->
    <!-- Scripts -->

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins.bundle.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('assets/css/prismjs.bundle.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.js" ></script>


    <!-- <link rel="stylesheet" href="{{asset('libs/datatable/dataTables.bootstrap4.min.css')}}" defer> -->


    <!-- <link rel="stylesheet" href="{{asset('libs/datatable/bootstrap.css')}}" defer> -->


     <!-- <link href="{{ asset('assets/css/fullcalendar.bundle.css') }}" rel="stylesheet">
     <link href="{{ asset('assets/css/datatables.bundle.css') }}" rel="stylesheet"> -->




    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->
 

    <!-- Styles -->

    <style type="text/css">
       .span_danger {
            color: red;
            display: inline-block;
            width: 100%;
        }
        .custom_image{
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            width:64px;
            height:64px;
        }
    </style>
    
</head>
<body>
    <!-- <div id="app"> -->
       <!--begin::Main-->
        
        <!--begin::Header Mobile-->
        <div id="kt_header_mobile" class="header-mobile bg-primary header-mobile-fixed d-print-none">
            <!--begin::Logo-->
            <a href="index.html">
                <img alt="Logo" src="{{ asset('libs/media/logos/logo-white.png') }}" class="max-h-30px" />
            </a>
            <!--end::Logo-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <button class="btn p-0 burger-icon burger-icon-left ml-4" id="kt_header_mobile_toggle">
                    <span></span>
                </button>
                <button class="btn p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                    <span class="svg-icon svg-icon-xl">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24" />
                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header Mobile-->



        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="d-flex flex-row flex-column-fluid page">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                    <!--begin::Header-->
                    <div id="kt_header" class="header flex-column header-fixed">
                        
                       <!--begin::Top-->
                        <!-- url_explode -->
                        <?php
                           
                            $sub_active         = 'menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here';
                            $sub_inactive       = 'menu-item menu-item';
                            $state              = 'justify-content-between';
                            
                            $user_state         = "";
                            $system_state       = "";
                            $house_state        = "";
                            $customer_state     = "";
                            $csr_dashboard      = "";
                            $os_state           = "";
                            $rs_state           = "";
                            $wh_dashboard       = "";
                            $route_plan_state   = "";
                            $scheduled_plan_state   = "";
                            $customer_service_state = "";

                            function make_active($data){
                                
                            }
                            function url_explode($url){
                               
                                $explodedUrl = explode("/", $url);
                                if(is_array($explodedUrl)){
                                    if (count($explodedUrl) > 1)
                                    {
                                        $main = $explodedUrl[0];
                                        $sub = $explodedUrl[1];
                                        $state ='show active';
                                        return $main;
                                    }else{
                                      
                                        return $url;
                                    }
                                }
                            }
                        ?>


<?php

    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    $uri = 'home';

    function find_url($link){
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        $uri = 'home';
        if ($uri==""){
            $uri = "dashboard.php";
        }
        $url = array();
        $main = 'tab-pane p-5 p-lg-0 justify-content-between';
        $sub = 'menu-item menu-item';
        $state = 'justify-content-between';


        if($uri == $link)
        { 
            $main = 'tab-pane py-5 p-lg-0 show active';
            $sub ='menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here';
            $state ='show active';
        }
        $url['main'] = $main;
        $url['sub'] = $sub;
        $url['state'] = $state;

        return $url;
}

?>
<style type="text/css">
    .label.label-inline{
        position: relative;
        bottom: 8px;
        border-radius: 50%;
            padding: 8px 8px 6px 8px;
    }
    .header-tabs .nav-item .nav-link{
        padding: 0.85rem 1rem !important
    }
    .header-menu .menu-nav > .menu-item > .menu-link .menu-text {
        font-size: 1rem !important;
    }
</style>
<div class="header-top d-print-none">
                            <!--begin::Container-->
                            <div class="container-fluid">
                                <!--begin::Left-->
                                <div class="d-none d-lg-flex align-items-center mr-3">
                                    <!--begin::Logo-->
                                    <a href="{{url('/home')}}" style="margin-right: 2rem !important">
                                        <img alt="Logo" src="{{ asset('libs/media/logos/logo-white.png') }}" style="width: 90px;height: 60px;"/>
                                    </a>
                                    <!--end::Logo-->
                                    <!--begin::Tab Navs(for desktop mode)-->
                                    <ul class="header-tabs nav align-self-end font-size-lg" role="tablist">
                                        <!--begin::Item-->
                                        <!-- System Management Menu -->
                                        <li class="nav-item mr-3">
                                            <a href="#" class="nav-link py-4 px-6
                                            <?php if(
                                                        ('home' == url_explode(request()->path()) )||
                                                        ('items' == url_explode(request()->path()) )||
                                                        ('services' == url_explode(request()->path()) )||
                                                        ('time_slots' == url_explode(request()->path()) )||
                                                        ('addons' == url_explode(request()->path()) )||
                                                        ('rate_lists' == url_explode(request()->path()) )||
                                                        ('addon_rate_lists' == url_explode(request()->path()) )||
                                                        
                                                        ('areas' == url_explode(request()->path()) )||
                                                        ('zones' == url_explode(request()->path()) )||
                                                        ('riders' == url_explode(request()->path()) )||
                                                        ('rider_incentives' == url_explode(request()->path()) )||
                                                        ('areas' == url_explode(request()->path()) )||
                                                        ('zones' == url_explode(request()->path()) )||
                                                        ('vehicle_types' == url_explode(request()->path()) )||
                                                        ('riders' == url_explode(request()->path()) )||
                                                        ('delivery_charges' == url_explode(request()->path()) )||
                                                        ('vats' == url_explode(request()->path()) )||
                                                        ('holidays' == url_explode(request()->path()) )
                                                    )
                                                    { echo " active"; 
                                                      $system_state = 'show active';
                                                    }
                                                else{ echo ""; } 
                                            ?>" 
                                            data-toggle="tab" data-target="#systems" role="tab">System </a>
                                        </li>

                                        <!-- User Management Menu -->
                                        @if(Gate::check('user-list') || Gate::check('permission-list') || Gate::check('role-list'))
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                            ('permissions' == url_explode(request()->path()) )||
                                                            ('roles' == url_explode(request()->path()) )||
                                                            ('users' == url_explode(request()->path()) )
                                                        )
                                                        {   echo " active"; 
                                                            $user_state = 'show active';
                                                        }
                                                    else{ echo "";
                                                        
                                                        } 
                                                ?>" 
                                                data-toggle="tab" data-target="#users" role="tab">User </a>
                                            </li>
                                        @endif
                                       
                                        <!-- Customer Management Menu -->
                                        @if(Gate::check('customer-list') || Gate::check('customer_type-list') || Gate::check('customer_wallet-list'))
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                            ('customers' == url_explode(request()->path()) )||
                                                            ('customer_types' == url_explode(request()->path()) )||
                                                            ('customer_wallets' == url_explode(request()->path()) )
                                                        )
                                                        { echo " active"; 
                                                            $customer_state = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#customers" role="tab">Customer </a>
                                            </li>
                                        @endif

                                        <!-- Houses Management Menu -->
                                        @if(Gate::check('distribution_hub-list') || Gate::check('wash_house-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                            ('distribution_hubs' == url_explode(request()->path()) )||
                                                            ('wash_houses' == url_explode(request()->path()) )
                                                        )
                                                        { echo " active"; 
                                                            $house_state = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#houses" role="tab">Houses </a>
                                            </li>
                                        @endif

                                        <!-- CSR - Dashboard -->
                                        @if(Gate::check('csr_dashboard-list') || Gate::check('order-list') || Gate::check('retainer-list') || Gate::check('complaint-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                            ('csr_dashboards' == url_explode(request()->path()) ) ||
                                                            ('orders' == url_explode(request()->path()) ) ||
                                                            ('retainer_days' == url_explode(request()->path()) )  ||
                                                            ('complaints' == url_explode(request()->path()) )
                                                            
                                                            
                                                        )
                                                        { echo " active"; 
                                                            $csr_dashboard = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#csr_dashboard" role="tab">CSR Dashboard </a>
                                            </li>
                                        @endif

                                        <!-- Customer Service Management Menu -->
                                        @if(Gate::check('order_verify-list') || Gate::check('Wash_house_order-list')|| Gate::check('Wash_house_summary-list')|| Gate::check('order_inspect-list')|| Gate::check('order_hfq-list')|| Gate::check('order_pack-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                            
                                                            ('order_verifies' == url_explode(request()->path()) )||
                                                            ('wash_house_orders' == url_explode(request()->path()) )||
                                                            ('wash_house_summaries' == url_explode(request()->path()) )||
                                                            ('order_inspects' == url_explode(request()->path()) )||
                                                            ('order_packs' == url_explode(request()->path()) )||
                                                            ('order_hfqs' == url_explode(request()->path()) )
                                                            
                                                            
                                                        )
                                                        { echo " active"; 
                                                            $customer_service_state = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#customer_services" role="tab">Orders </a>
                                            </li>
                                        @endif



                                        <!-- RS Dashboard -->
                                        @if(Gate::check('schedule_route_plan-list') || Gate::check('scheduled_route_plan-list') || Gate::check('report-list') || Gate::check('fuel-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                        
                                                            ('route_plans' == url_explode(request()->path()) ) ||
                                                            ('scheduled_plans' == url_explode(request()->path()) ) ||
                                                            ('reports' == url_explode(request()->path()) )  ||
                                                            ('fuel_reports' == url_explode(request()->path()) )
                                                           
                                                        )
                                                        { echo " active"; 
                                                            $rs_state = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#rs_dashboard" role="tab">RS Dashboard </a>
                                            </li>
                                        @endif
                                        
                                        <!-- OS Dashboard -->
                                        @if(Gate::check('packing-list') || Gate::check('tagging-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php if(
                                                        
                                                            ('packings' == url_explode(request()->path()) ) ||
                                                            ('taggings' == url_explode(request()->path()) ) 
                                                        )
                                                        { echo " active"; 
                                                            $os_state = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#os_dashboard" role="tab">OS Dashboard </a>
                                            </li>
                                        @endif

                                        <!-- Wash house Dashboard -->
                                        @if(Gate::check('wh_dashboard-list') || Gate::check('wh_billing-list') )
                                            <li class="nav-item mr-3">
                                                <a href="#" class="nav-link py-4 px-6
                                                <?php 
                                                        if(
                                                        
                                                            ('wh_dashboards' == url_explode(request()->path()) ) ||
                                                            ('wh_billings' == url_explode(request()->path()) ) 
                                                        )
                                                        {   echo " active"; 
                                                            $wh_dashboard = 'show active';
                                                        }
                                                
                                                ?>" 
                                                data-toggle="tab" data-target="#wh_dashboard" role="tab">WH Dashboard </a>
                                            </li>
                                        @endif

                                        
                                        
                                    </ul>
                                    <!--begin::Tab Navs-->
                                </div>
                                <!--end::Left-->
                                <!--begin::Topbar-->
                                <div class="topbar bg-primary">
                                    <!--begin::User-->
                                    <div class="topbar-item">
                                        <div class="btn btn-icon btn-hover-transparent-white w-lg-auto d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                                            <?php 
                                                    $string = Auth::user()->name; 
                                                    $name   = explode(' ',trim($string));
                                                    $letter = strtoupper($string[0]);
                                            ?>
                                            <div class="d-flex flex-column text-right pr-lg-3">
                                                <!-- <span class="text-white opacity-50 font-weight-bold font-size-sm d-none d-md-inline"></span> -->
                                                <span class="text-white font-weight-bolder font-size-sm d-none d-md-inline">{{$name[0]}}</span>
                                            </div>
                                            <span class="symbol symbol-35">
                                          
                                                <span class="symbol-label font-size-h5 font-weight-bold text-white bg-white-o-30">{{$letter}}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <!--end::User-->
                                </div>
                                <!--end::Topbar-->
                            </div>
                            <!--end::Container-->
                        </div>


                        <!--end::Top-->
                        <!--begin::Bottom-->
                        <div class="header-bottom">
                            <!--begin::Container-->
                            <div class="container-fluid">
                                <!--begin::Header Menu Wrapper-->
                                <div class="header-navs header-navs-left" id="kt_header_navs">
                                
                                    <!--begin::Tab Content-->
                                    <div class="tab-content">

                                        <!--begin::System Pane-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $system_state; ?>"  id="systems">
                                            <!--begin::Menu-->
                                            <div id="systems" class="header-menu header-menu-mobile header-menu-layout-default">
                                                <!--begin::Nav-->

                                                <ul class="menu-nav">
                                                    <li class="@if('home' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                        aria-haspopup="true">
                                                        <a href="{{url('/home')}}" class="menu-link">
                                                            <span class="menu-text">Dashboard</span>
                                                        </a>
                                                    </li>
                                                    @can('item-list')
                                                        <li class="@if('items' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/items')}}" class="menu-link">
                                                                <span class="menu-text">Items</span>
                                                            </a>
                                                        </li>
                                                    @endcan('item-list')

                                                    @can('service-list')
                                                        <li class="@if('services' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/services')}}" class="menu-link">
                                                                <span class="menu-text">Services</span>
                                                            </a>
                                                        </li>
                                                    @endcan('service-list')

                                                    @can('rate_list-list')
                                                        <li class="@if('rate_lists' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/rate_lists')}}" class="menu-link">
                                                                <span class="menu-text">Service Rate </span>
                                                            </a>
                                                        </li>
                                                    @endcan('rate_list-list')

                                                    @can('addon-list')
                                                        <li class="@if('addons' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/addons')}}" class="menu-link">
                                                                <span class="menu-text">Addons</span>
                                                            </a>
                                                        </li>
                                                    @endcan('addon-list')

                                                    @can('addon_rate_list-list')
                                                        <li class="@if('addon_rate_lists' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/addon_rate_lists')}}" class="menu-link">
                                                                <span class="menu-text">Addon Rate </span>
                                                            </a>
                                                        </li>
                                                    @endcan('addon_rate_list-list')

                                                    @can('time_slot-list')
                                                        <li class="@if('time_slots' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/time_slots')}}" class="menu-link">
                                                                <span class="menu-text">Timeslots</span>
                                                            </a>
                                                        </li>
                                                    @endcan('time_slot-list')

                                                    @can('rider-list')
                                                        <li class="@if('riders' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/riders')}}" class="menu-link">
                                                                <span class="menu-text">Riders</span>
                                                            </a>
                                                        </li>
                                                    @endcan('rider-list')
                                            
                                                    
                                                    @can('vehicle_type-list')
                                                        <li class="@if('vehicle_types' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/vehicle_types')}}" class="menu-link">
                                                                <span class="menu-text">V.Types</span>
                                                            </a>
                                                        </li>
                                                    @endcan('vehicle_type-list')

                                                    @can('area-list')
                                                        <li class="@if('areas' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/areas')}}" class="menu-link">
                                                                <span class="menu-text">Areas</span>
                                                            </a>
                                                        </li>
                                                    @endcan('area-list')

                                                    @can('zone-list')
                                                        <li class="@if('zones' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/zones')}}" class="menu-link">
                                                                <span class="menu-text">Zones</span>
                                                            </a>
                                                        </li>
                                                    @endcan('zone-list')

                                                    @can('delivery_charge-list')
                                                        <li class="@if(('delivery_charges' == url_explode(request()->path())) ||'vats' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/delivery_charges')}}" class="menu-link">
                                                                <span class="menu-text">D.Charges</span>
                                                            </a>
                                                        </li>
                                                    @endcan('delivery_charge-list')

                                                    @can('holiday-list')
                                                        <li class="@if('holidays' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/holidays')}}" class="menu-link">
                                                                <span class="menu-text">Holidays</span>
                                                            </a>
                                                        </li>
                                                    @endcan('holiday-list')
                                                    <li class="@if('rider_incentives' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                            aria-haspopup="true">
                                                            <a href="{{url('/rider_incentives')}}" class="menu-link">
                                                                <span class="menu-text">Rider Compensation</span>
                                                            </a>
                                                        </li>
                                                        
                                                </ul>
                                                <!--end::Nav-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::System Pane-->

                                        <!--begin::User Pane-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $user_state; ?>"  id="users">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                        @can('permission-list')
                                                            <li class="@if('permissions' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/permissions')}}" class="menu-link">
                                                                    <span class="menu-text">Permission</span>
                                                                </a>
                                                            </li>
                                                        @endcan('permission-list')

                                                        @can('role-list')
                                                            <li class="@if('roles' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/roles')}}" class="menu-link">
                                                                    <span class="menu-text">Role</span>
                                                                </a>
                                                            </li>
                                                        @endcan('role-list')

                                                        @can('user-list')
                                                            <li class="@if('users' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/users')}}" class="menu-link">
                                                                    <span class="menu-text">User</span>
                                                                </a>
                                                            </li>
                                                        @endcan('user-list')
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::User Pane-->

                                        <!--begin::house Pane-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $house_state; ?>"  id="houses">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                        @can('wash_house-list')
                                                            <li class="@if('wash_houses' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/wash_houses')}}" class="menu-link">
                                                                    <span class="menu-text">Wash-House</span>
                                                                </a>
                                                            </li>
                                                        @endcan('wash_house-list')

                                                        @can('distribution_hub-list')
                                                            <li class="@if('distribution_hubs' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/distribution_hubs')}}" class="menu-link">
                                                                    <span class="menu-text">Distribution Hub</span>
                                                                </a>
                                                            </li>
                                                        @endcan('distribution_hub-list')
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::house Pane-->

                                        <!--begin::Customer Pane-->
                                         <div class="tab-pane py-5 p-lg-0 <?php echo $customer_state; ?>"  id="customers">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">

                                                        @can('customer-list')
                                                            <li class="@if('customers' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/customers')}}" class="menu-link">
                                                                    <span class="menu-text">Customer</span>
                                                                </a>
                                                            </li>
                                                        @endcan('customer-list')

                                                        @can('customer_type-list')
                                                            <li class="@if('customer_types' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/customer_types')}}" class="menu-link">
                                                                    <span class="menu-text">Customer Types</span>
                                                                </a>
                                                            </li>
                                                        @endcan('customer_type-list')

                                                        @can('customer_wallet-list')
                                                            <li class="@if('customer_wallets' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/customer_wallets')}}" class="menu-link">
                                                                    <span class="menu-text">Customer Wallet</span>
                                                                </a>
                                                            </li>
                                                        @endcan('customer_wallet-list')

                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Customer Pane-->

                                        <!--begin::Customer Services Pane-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $customer_service_state; ?>"  id="customer_services">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                       
                                                        @can('order_verify-list')
                                                            <li class="@if('order_verifies' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/order_verifies')}}" class="menu-link">
                                                                    <span class="menu-text">Verify Order</span>
                                                                </a>
                                                            </li>
                                                        @endcan('order_verify-list')

                                                        @can('Wash_house_order-list')
                                                            <li class="@if('wash_house_orders' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/wash_house_orders')}}" class="menu-link">
                                                                    <!-- <span class="menu-text">Wash-house Order</span> -->
                                                                    <span class="menu-text">Change Wash-house </span>
                                                                </a>
                                                            </li>
                                                        @endcan('Wash_house_order-list')

                                                        @can('Wash_house_summary-list')
                                                            <li class="@if('wash_house_summaries' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/wash_house_summaries')}}" class="menu-link">
                                                                    <span class="menu-text">Wash-house Summary</span>
                                                                </a>
                                                            </li>
                                                        @endcan('Wash_house_summary-list')

                                                        @can('order_inspect-list')
                                                            <li class="@if('order_inspects' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/order_inspects')}}" class="menu-link">
                                                                    <span class="menu-text">Inspect Order</span>
                                                                </a>
                                                            </li>
                                                        @endcan('order_inspect-list')

                                                        @can('order_hfq-list')
                                                            <li class="@if('order_hfqs' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/order_hfqs')}}" class="menu-link">
                                                                    <span class="menu-text">HFQ Order</span>
                                                                </a>
                                                            </li>
                                                        @endcan('order_hfq-list')
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Customer Pane-->

                                        <!-- BEGIN: CSR - Dashboard -->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $csr_dashboard; ?>"  id="csr_dashboard">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                        @can('csr_dashboard-list')
                                                            <li class="@if('csr_dashboards' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/csr_dashboards')}}" class="menu-link">
                                                                    <span class="menu-text">CSR Dashboard</span>
                                                                </a>
                                                            </li>
                                                        @endcan('csr_dashboard-list')

                                                        @can('order-list')
                                                            <li class="@if('orders' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/orders')}}" class="menu-link">
                                                                    <span class="menu-text">All Orders</span>
                                                                </a>
                                                            </li>
                                                        @endcan('order-list')

                                                        @can('retainer-list')
                                                            <li class="@if('retainer_days' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/retainer_days')}}" class="menu-link">
                                                                    <span class="menu-text">All Retainers</span>
                                                                </a>
                                                            </li>
                                                        @endcan('retainer-list')

                                                        @can('complaint-list')
                                                            <li class="@if('complaints' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/complaints')}}" class="menu-link">
                                                                    <span class="menu-text">Complaints</span>
                                                                </a>
                                                            </li>
                                                        @endcan('complaint-list')
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!-- END: CSR - Dashboard -->

                                        <!--begin::RS Dashboard-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $rs_state; ?>"  id="rs_dashboard">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                        @can('schedule_route_plan-list')
                                                            <li class="@if('route_plans' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                    <a href="{{url('/route_plans')}}" class="menu-link">
                                                                        <span class="menu-text">Schedule Route Plan</span>
                                                                    </a>
                                                                </li>
                                                        @endcan('schedule_route_plan-list')

                                                        @can('scheduled_route_plan-list')
                                                            <li class="@if('scheduled_plans' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                    <a href="{{url('/scheduled_plans')}}" class="menu-link">
                                                                        <span class="menu-text">Scheduled Route Plan</span>
                                                                    </a>
                                                                </li>
                                                        @endcan('scheduled_route_plan-list')

                                                        @can('report-list')
                                                            <li class="@if('reports' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                    <a href="{{url('/reports')}}" class="menu-link">
                                                                        <span class="menu-text">Reports</span>
                                                                    </a>
                                                                </li>
                                                        @endcan('report-list')
                                                        
                                                        <li class="@if('fuel_report' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                    <a href="{{url('/fuel_report')}}" class="menu-link">
                                                                        <span class="menu-text">Fuel Reports</span>
                                                                    </a>
                                                        </li>
                                                        
                                                          
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::RS Dashboard-->

                                        <!--begin::OS Dashboard-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $os_state; ?>"  id="os_dashboard">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                        <ul class="menu-nav">
                                                            @can('packing-list')
                                                                <li class="@if('packings' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                        <a href="{{url('/packings')}}" class="menu-link">
                                                                        <span class="menu-text">Packing</span>
                                                                    </a>
                                                                </li>
                                                            @endcan('packing-list')

                                                            @can('tagging-list')
                                                                <li class="@if('taggings' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                    aria-haspopup="true">
                                                                    <a href="{{url('/taggings')}}" class="menu-link">
                                                                        <span class="menu-text">Tagging</span>
                                                                    </a>
                                                                </li>
                                                            @endcan('tagging-list')
                                                        </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::OS Dashboard-->
                                        
                                        <!--begin::Wash house Dashboard-->
                                        <div class="tab-pane py-5 p-lg-0 <?php echo $wh_dashboard; ?>"  id="wh_dashboard">
                                            <!--begin::Actions-->
                                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                                                <!--begin::Menu-->
                                                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                                                    <!--begin::Nav-->
                                                    <ul class="menu-nav">
                                                        @can('wh_dashboard-list')
                                                            <li class="@if('wh_dashboards' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/wh_dashboards')}}" class="menu-link">
                                                                    <span class="menu-text">Dashboard</span>
                                                                </a>
                                                            </li>
                                                        @endcan('wh_dashboard-list')

                                                        @can('wh_billing-list')
                                                            <li class="@if('wh_billings' == url_explode(request()->path()) ) {{ $sub_active}}@else{{  $sub_inactive}} @endif"
                                                                aria-haspopup="true">
                                                                <a href="{{url('/wh_billings')}}" class="menu-link">
                                                                    <span class="menu-text">Billing</span>
                                                                </a>
                                                            </li>
                                                        @endcan('wh_billing-list')
                                                    </ul>
                                                    <!--end::Nav-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Wash house Dashboard-->

                                        
                                       
                                    </div>
                                    <!--end::Tab Content-->
                                </div>
                                <!--end::Header Menu Wrapper-->
                            </div>
                            <!--end::Container-->
                        </div>
                        <!--end::Bottom-->
                        
                    </div>

                    <!-- <script src="{{asset('js/app.js')}}" ></script> -->
                    <script src="{{asset('libs/jquery.min.js')}}" ></script>

                      <!-- <script src="{{ asset('libs/plugins/custom/prismjs/prismjs.bundle.js') }}" ></script> -->
                      <script src="{{ asset('libs/js/scripts.bundle.js') }}" ></script>
                      <!-- <script src="{{ asset('libs/js/card.js') }}" ></script> -->
                      <!-- <script src="{{ asset('libs/js/pages/features/cards/tools.js') }}" ></script> -->
                      <script src="{{ asset('libs/plugins/global/plugins.bundle.js') }}"  ></script>
                      <!-- <script src="{{ asset('libs/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}" ></script> -->
                      <!-- <script src="{{ asset('libs/js/pages/widgets.js') }}"></script> -->
                      <!-- <script src="{{asset('libs/js/pages/crud/file-upload/image-input.js')}}"></script> -->
                      <!-- <script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
                      <script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script> -->
                      <script src="{{asset('libs/jquery.validate.js')}}" defer></script>
                        

                    <!--end::Header-->
                    <!--begin::Content-->
                    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                        <!--begin::Entry-->
                        <div class="d-flex flex-column-fluid">
                            <!--begin::Container-->
                            <div class="container-fluid">
                                <!--begin::Dashboard-->
                                <!--begin::Row-->
                                @yield('content')
                                
                                <!--end::Row-->
                                <!--end::Dashboard-->
                            </div>
                            <!--end::Container-->
                        </div>
                        <!--end::Entry-->
                    </div>
                    <!--end::Content-->
                   <!--begin::Footer-->
                    <div class="footer bg-white py-4 d-flex flex-lg-column d-print-none" id="kt_footer">
                        <!--begin::Container-->
                        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <!--begin::Copyright-->
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted font-weight-bold mr-2">2020©</span>
                                <a href="index.php" target="_blank" class="text-dark-75 text-hover-primary">WashUp</a>
                            </div>
                            <!--end::Copyright-->
                            <!--begin::Nav-->
                            <div class="nav nav-dark order-1 order-md-2">
                                <a href="#"  class="nav-link pr-3 pl-0">About</a>
                                <a href="#"  class="nav-link px-3">Team</a>
                                <a href="#"  class="nav-link pl-3 pr-0">Contact</a>
                            </div>
                            <!--end::Nav-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Main-->

    </div>


    <!-- begin::User Panel-->
        <div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
            <!--begin::Header-->
            <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
                <h3 class="font-weight-bold m-0">User Profile
                <small class="text-muted font-size-sm ml-2"></small></h3>
                <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
                    <i class="ki ki-close icon-xs text-muted"></i>
                </a>
            </div>
            <!--end::Header-->
            <!--begin::Content-->
            <div class="offcanvas-content pr-5 mr-n5">
                <!--begin::Header-->
                <div class="d-flex align-items-center mt-5">
                    <div class="symbol symbol-100 mr-5">
                        <?php if(Auth::user()->image){?>
                        <img src="uploads/users/{{Auth::user()->image}}" class="symbol-label"  alt="User Image">
                        <?php }else{ ?>
                            <div class="symbol-label" style="background-image:url('{{ asset('libs/media/users/300_21.jpg') }}')"></div>
                        <?php }?>
                       
                        <i class="symbol-badge bg-success"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{Auth::user()->name  }}</a>
                        <!-- <div class="text-muted mt-1">Application Developer</div> -->
                        <div class="navi mt-2">
                            <a href="#" class="navi-item">
                                <span class="navi-link p-0 pb-2">
                                    <span class="navi-icon mr-1">
                                        <span class="svg-icon svg-icon-lg svg-icon-primary">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000" />
                                                    <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </span>
                                    <span class="navi-text text-muted text-hover-primary">{{Auth::user()->email}}</span>
                                </span>
                            </a>

                            <a class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off"></i>
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <!--end::Header-->
              
                <!--end::Nav-->
               
            </div>
            <!--end::Content-->
        </div>
        <!-- end::User Panel-->
        <!--begin::Quick Cart-->
        <div id="kt_quick_cart" class="offcanvas offcanvas-right p-10">
            <!--begin::Header-->
            <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
                <h4 class="font-weight-bold m-0">Shopping Cart</h4>
                <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_cart_close">
                    <i class="ki ki-close icon-xs text-muted"></i>
                </a>
            </div>
            <!--end::Header-->
            <!--begin::Content-->
           
            <!--end::Content-->
        </div>
        <!--end::Quick Cart-->
        <!--begin::Quick Panel-->
        <div id="kt_quick_panel" class="offcanvas offcanvas-right pt-5 pb-10">
            <!--begin::Header-->
            <div class="offcanvas-header offcanvas-header-navs d-flex align-items-center justify-content-between mb-5">
                <ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-primary flex-grow-1 px-10" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_logs">Audit Logs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_notifications">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_settings">Settings</a>
                    </li>
                </ul>
                <div class="offcanvas-close mt-n1 pr-5">
                    <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_panel_close">
                        <i class="ki ki-close icon-xs text-muted"></i>
                    </a>
                </div>
            </div>
            <!--end::Header-->
            <!--begin::Content-->
          
            <!--end::Content-->
        </div>
        <!--end::Quick Panel-->
        <!--begin::Chat Panel-->
        <div class="modal modal-sticky modal-sticky-bottom-right" id="kt_chat_modal" role="dialog" data-backdrop="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!--begin::Card-->
                    <div class="card card-custom">
                        <!--begin::Header-->
                        <div class="card-header align-items-center px-4 py-3">
                            <div class="text-left flex-grow-1">
                                <!--begin::Dropdown Menu-->
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="svg-icon svg-icon-lg">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Add-user.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                    <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </button>
                                    
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                            <div class="text-center flex-grow-1">
                                <div class="text-dark-75 font-weight-bold font-size-h5">Matt Pears</div>
                                <div>
                                    <span class="label label-dot label-success"></span>
                                    <span class="font-weight-bold text-muted font-size-sm">Active</span>
                                </div>
                            </div>
                            <div class="text-right flex-grow-1">
                                <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-dismiss="modal">
                                    <i class="ki ki-close icon-1x"></i>
                                </button>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body">
                            <!--begin::Scroll-->
                            <div class="scroll scroll-pull" data-height="375" data-mobile-height="300">
                                <!--begin::Messages-->
                                <div class="messages">
                                    <!--begin::Message In-->
                                    <div class="d-flex flex-column mb-5 align-items-start">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-40 mr-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_12.jpg') }}" />
                                            </div>
                                            <div>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Matt Pears</a>
                                                <span class="text-muted font-size-sm">2 Hours</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">How likely are you to recommend our company to your friends and family?</div>
                                    </div>
                                    <!--end::Message In-->
                                    <!--begin::Message Out-->
                                    <div class="d-flex flex-column mb-5 align-items-end">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-muted font-size-sm">3 minutes</span>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">You</a>
                                            </div>
                                            <div class="symbol symbol-circle symbol-40 ml-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_21.jpg') }}" />
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">Hey there, we’re just writing to let you know that you’ve been subscribed to a repository on GitHub.</div>
                                    </div>
                                    <!--end::Message Out-->
                                    <!--begin::Message In-->
                                    <div class="d-flex flex-column mb-5 align-items-start">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-40 mr-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_21.jpg') }}" />
                                            </div>
                                            <div>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Matt Pears</a>
                                                <span class="text-muted font-size-sm">40 seconds</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">Ok, Understood!</div>
                                    </div>
                                    <!--end::Message In-->
                                    <!--begin::Message Out-->
                                    <div class="d-flex flex-column mb-5 align-items-end">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-muted font-size-sm">Just now</span>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">You</a>
                                            </div>
                                            <div class="symbol symbol-circle symbol-40 ml-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_21.jpg') }}" />
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">You’ll receive notifications for all issues, pull requests!</div>
                                    </div>
                                    <!--end::Message Out-->
                                    <!--begin::Message In-->
                                    <div class="d-flex flex-column mb-5 align-items-start">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-40 mr-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_12.jpg') }}" />
                                            </div>
                                            <div>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Matt Pears</a>
                                                <span class="text-muted font-size-sm">40 seconds</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">You can unwatch this repository immediately by clicking here:
                                        <a href="#">https://github.com</a></div>
                                    </div>
                                    <!--end::Message In-->
                                    <!--begin::Message Out-->
                                    <div class="d-flex flex-column mb-5 align-items-end">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-muted font-size-sm">Just now</span>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">You</a>
                                            </div>
                                            <div class="symbol symbol-circle symbol-40 ml-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_21.jpg') }}" />
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">Discover what students who viewed Learn Figma - UI/UX Design. Essential Training also viewed</div>
                                    </div>
                                    <!--end::Message Out-->
                                    <!--begin::Message In-->
                                    <div class="d-flex flex-column mb-5 align-items-start">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-40 mr-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_12.jpg') }}" />
                                            </div>
                                            <div>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Matt Pears</a>
                                                <span class="text-muted font-size-sm">40 seconds</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">Most purchased Business courses during this Delivery!</div>
                                    </div>
                                    <!--end::Message In-->
                                    <!--begin::Message Out-->
                                    <div class="d-flex flex-column mb-5 align-items-end">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-muted font-size-sm">Just now</span>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">You</a>
                                            </div>
                                            <div class="symbol symbol-circle symbol-40 ml-3">
                                                <img alt="Pic" src="{{ asset('libs/media/users/300_21.jpg') }}" />
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">Company BBQ to celebrate the last quater achievements and goals. Food and drinks provided</div>
                                    </div>
                                    <!--end::Message Out-->
                                </div>
                                <!--end::Messages-->
                            </div>
                            <!--end::Scroll-->
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer align-items-center">
                            <!--begin::Compose-->
                            <textarea class="form-control border-0 p-0" rows="2" placeholder="Type a message"></textarea>
                            <div class="d-flex align-items-center justify-content-between mt-5">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-clean btn-icon btn-md mr-1">
                                        <i class="flaticon2-photograph icon-lg"></i>
                                    </a>
                                    <a href="#" class="btn btn-clean btn-icon btn-md">
                                        <i class="flaticon2-photo-camera icon-lg"></i>
                                    </a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">Send</button>
                                </div>
                            </div>
                            <!--begin::Compose-->
                        </div>
                        <!--end::Footer-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Chat Panel-->
        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop">
            <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
                        <path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
        </div>
        <!--end::Scrolltop-->
        <!--begin::Sticky Toolbar-->
    
        <!--end::Sticky Toolbar-->
        <!--begin::Demo Panel-->
        
        <!--end::Demo Panel-->
        <!-- <script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script> -->
        
        <!--begin::Global Config(global config for global JS scripts)-->
        <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
        <!--end::Global Config-->


            <script type="text/javascript">
                $(document).ready(function () {
                   $(document).on('click','.delete_all', function(e) {
                        var id = $(this).data('id');
                         var allVals = [];
                             allVals.push($(this).attr('data-id'));

                        if(allVals.length <=0)
                        {
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please select row!',
                            // footer: '<a href>Why do I have this issue?</a>'
                            })
                        }  else {

                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                // console.log(result.value);
                            var join_selected_values = allVals.join(",");

                            $.ajax({
                                url: $(this).data('url'),
                                type: 'DELETE',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: 'ids='+join_selected_values,
                                success: function (data) {
                                    if (data['success']) {
                                        $('#myTable').DataTable().ajax.reload();
                                        swalWithBootstrapButtons.fire(
                                            'Deleted!',
                                            data['success'],
                                            'success'
                                        )

                                    } else if (data['error']) {
                                        // alert(data['error']);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data['error'],
                                        })
                                    } else {
                                        alert(data['error']);
                                    }
                                },
                                error: function (data) {
                                    // console.log(data);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: "It is forign key of another entity, \n It cannot be deleted",
                                        })
                                    // alert(data.responseText);
                                }
                            });


                            } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                            ) {
                            swalWithBootstrapButtons.fire(
                                'Cancelled',
                                'Your imaginary data is safe :)',
                                'error'
                            )
                            }
                        })

                        }
                    });



                });


        </script>
 

  


       
      
</body>
</html>
