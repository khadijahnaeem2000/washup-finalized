<?php



use Illuminate\Support\Facades\Route;



// Route::get('/', function () {

//     return view('welcome');

// });



Auth::routes();

Route::get('/', function () {

    if(Auth::check()) {

        return redirect('/home');

    } else {

        return view('auth.login');

    }

});

	//start khadeeja's edit
	Route::post('/update_status', [App\Http\Controllers\ServiceController::class, 'updateStatus'])->name('update_status');
	//end khadeeja's edit
	Route::delete('/service_delete', [App\Http\Controllers\ServiceController::class, 'destroy']);


Route::get('/sendMail',[App\Http\Controllers\MailController::class, 'sendMail']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');





Route::get('/send_test/{email}',[App\Http\Controllers\MailController::class, 'send_test']);



Route::post('/fetch_dashboard', [App\Http\Controllers\HomeController::class, 'fetch_dashboard']);



Route::post('/remove_orders', [App\Http\Controllers\HomeController::class, 'remove_orders']);



// Route::post('/export_csv', [App\Http\Controllers\HomeController::class, 'export']);



Route::post('/reverse_orders', [App\Http\Controllers\HomeController::class, 'reverse_orders']);



Route::group(['middleware' => ['auth']], function() {

    

        

    // Route::get('exp_backup', function () {

    

    //     \Artisan::call('database:backup');

    

    //     dd("Database backup stored successfully");

    

    // });



    Route::post('/export_hfq_items', [App\Http\Controllers\Order_hfqController::class, 'export_hfq_items']);

	

	Route::resource('/home_dashboard', App\Http\Controllers\HomeController::class);

	

	Route::get('/send_cancel_mail/{id}',[App\Http\Controllers\MailController::class, 'send_cancel_mail']);



	Route::get('/send_invoice/{id}',[App\Http\Controllers\MailController::class, 'send_invoice']);

	Route::get('/send_sms',[App\Http\Controllers\NotificationController::class, 'send_sms']);

	Route::get('/order_modified/{id}',[App\Http\Controllers\NotificationController::class, 'order_modified']);

	

	Route::get('/test/{id}',[App\Http\Controllers\NotificationController::class, 'complaint_msg']);

	Route::get('/test2/{id}',[App\Http\Controllers\Wash_house_has_summaryController::class, 'get_delivery_date']);

	// Route::get('sendhtmlemail','MailController@html_email');

	// Route::get('sendattachmentemail','MailController@attachment_email');
    Route::post('/waver_delivery_request', [App\Http\Controllers\Order_verifyController::class, 'waver_delivery_request'])->name('waver_delivery_request');
    Route::post('/waver_deliverys', [App\Http\Controllers\Order_verifyController::class, 'waver_deliverys'])->name('waver_deliverys');

	Route::resource('/items', App\Http\Controllers\ItemController::class);

	Route::get('/item_list', [App\Http\Controllers\ItemController::class, 'list']);

	Route::delete('/item_delete', [App\Http\Controllers\ItemController::class, 'destroy']);



	Route::resource('/services', App\Http\Controllers\ServiceController::class);

	Route::get('/service_list', [App\Http\Controllers\ServiceController::class, 'list']);

	Route::delete('/service_delete', [App\Http\Controllers\ServiceController::class, 'destroy']);

    
	Route::post('/update_order_number', [App\Http\Controllers\ServiceController::class, 'updateOrder'])->name('update_order_number');

	Route::resource('/rate_lists', App\Http\Controllers\Rate_listController::class);

	Route::get('/rate_list_list', [App\Http\Controllers\Rate_listController::class, 'list']);

	Route::delete('/rate_list_delete', [App\Http\Controllers\Rate_listController::class, 'destroy']);

	Route::post('/rep_service_rate_list', [App\Http\Controllers\Rate_listController::class, 'rep_service_rate_list']);

	Route::post('/fetch_services', [App\Http\Controllers\Rate_listController::class, 'fetch_services']);

	Route::post('/fetch_items_list', [App\Http\Controllers\Rate_listController::class, 'fetch_items']);



	Route::resource('/addons', App\Http\Controllers\AddonController::class);

	Route::get('/addon_list', [App\Http\Controllers\AddonController::class, 'list']);

	Route::delete('/addon_delete', [App\Http\Controllers\AddonController::class, 'destroy']);



	Route::resource('/addon_rate_lists', App\Http\Controllers\Addon_rate_listController::class);

	Route::get('/addon_rate_list_list', [App\Http\Controllers\Addon_rate_listController::class, 'list']);

	Route::delete('/addon_rate_list_delete', [App\Http\Controllers\Addon_rate_listController::class, 'destroy']);

	Route::post('/rep_addon_rate_list', [App\Http\Controllers\Addon_rate_listController::class, 'rep_addon_rate_list']);

	Route::post('/fetch_addon_lists', [App\Http\Controllers\Addon_rate_listController::class, 'fetch_addon_lists']);



	Route::resource('/time_slots', App\Http\Controllers\Time_slotController::class);

	Route::get('/timeslot_list', [App\Http\Controllers\Time_slotController::class, 'list']);

	Route::delete('/timeslot_delete', [App\Http\Controllers\Time_slotController::class, 'destroy']);



	Route::resource('/riders', App\Http\Controllers\RiderController::class);

	Route::get('/rider_list', [App\Http\Controllers\RiderController::class, 'list']);

	Route::delete('/rider_delete', [App\Http\Controllers\RiderController::class, 'destroy']);



	Route::resource('/vehicle_types', App\Http\Controllers\Vehicle_typeController::class);

	Route::get('/vehicle_type_list', [App\Http\Controllers\Vehicle_typeController::class, 'list']);

	Route::delete('/vehicle_type_delete', [App\Http\Controllers\Vehicle_typeController::class, 'destroy']);



	Route::resource('/areas', App\Http\Controllers\AreaController::class);

	Route::get('/area_list', [App\Http\Controllers\AreaController::class, 'list']);

	Route::delete('/area_delete', [App\Http\Controllers\AreaController::class, 'destroy']);



	Route::resource('/zones', App\Http\Controllers\ZoneController::class);

	Route::get('/zone_list', [App\Http\Controllers\ZoneController::class, 'list']);

	Route::delete('/zone_delete', [App\Http\Controllers\ZoneController::class, 'destroy']);



	Route::resource('/holidays', App\Http\Controllers\HolidayController::class);

	Route::get('/holiday_list', [App\Http\Controllers\HolidayController::class, 'list']);

	Route::delete('/holiday_delete', [App\Http\Controllers\HolidayController::class, 'destroy']);



	Route::resource('/delivery_charges', App\Http\Controllers\Delivery_chargeController::class);

	Route::get('/delivery_charge_list', [App\Http\Controllers\Delivery_chargeController::class, 'list']);

	Route::delete('/delivery_charge_delete', [App\Http\Controllers\Delivery_chargeController::class, 'destroy']);





	Route::resource('/vats', App\Http\Controllers\VatController::class);

	Route::get('/vat_list', [App\Http\Controllers\VatController::class, 'list']);

	// Route::delete('/delivery_charge_delete', [App\Http\Controllers\Delivery_chargeController::class, 'destroy']);



	Route::resource('/permissions', App\Http\Controllers\PermissionController::class);

	Route::get('/permission_list', [App\Http\Controllers\PermissionController::class, 'list']);

	Route::delete('/permission_delete', [App\Http\Controllers\PermissionController::class, 'destroy']);



	Route::resource('/roles', App\Http\Controllers\RoleController::class);

	Route::get('/role_list', [App\Http\Controllers\RoleController::class, 'list']);

	Route::delete('/role_delete', [App\Http\Controllers\RoleController::class, 'destroy']);



	Route::resource('/users', App\Http\Controllers\UserController::class);

	Route::get('/user_list', [App\Http\Controllers\UserController::class, 'list']);

	Route::delete('/user_delete', [App\Http\Controllers\UserController::class, 'destroy']);



	Route::resource('/customers', App\Http\Controllers\CustomerController::class);

	Route::get('/customer_list', [App\Http\Controllers\CustomerController::class, 'list']);

	Route::delete('/customer_delete', [App\Http\Controllers\CustomerController::class, 'destroy']);



	Route::resource('/customer_types', App\Http\Controllers\Customer_typeController::class);

	Route::get('/customer_typeList', [App\Http\Controllers\Customer_typeController::class, 'list']);

	Route::delete('/customer_type_delete', [App\Http\Controllers\Customer_typeController::class, 'destroy']);



	Route::resource('/customer_wallets', App\Http\Controllers\Customer_walletController::class);

	Route::post('customer_wallets/customer_wallet_list', [App\Http\Controllers\Customer_walletController::class, 'list']);

	Route::post('customer_wallets/customer_wallet_list_onload', [App\Http\Controllers\Customer_walletController::class, 'list_onload']);

	Route::delete('/customer_wallet_delete', [App\Http\Controllers\Customer_walletController::class, 'destroy']);

	Route::post('/fetch_customer_detail', [App\Http\Controllers\Customer_walletController::class, 'fetch_customer_detail']);

	Route::post('/fetch_email_details', [App\Http\Controllers\Customer_walletController::class, 'fetch_email_detail']);



	Route::resource('/wash_houses', App\Http\Controllers\Wash_houseController::class);

	Route::get('/wash_house_list', [App\Http\Controllers\Wash_houseController::class, 'list']);

	Route::delete('/wash_house_delete', [App\Http\Controllers\Wash_houseController::class, 'destroy']);

	Route::post('/fetch_zones', [App\Http\Controllers\Wash_houseController::class, 'fetch_zones']);



	Route::resource('/distribution_hubs', App\Http\Controllers\Distribution_hubController::class);

	Route::get('/distribution_hub_list', [App\Http\Controllers\Distribution_hubController::class, 'list']);

	Route::delete('/distribution_hub_delete', [App\Http\Controllers\Distribution_hubController::class, 'destroy']);

	Route::post('/fetch_timeslot', [App\Http\Controllers\Distribution_hubController::class, 'fetch_timeslot']);





	Route::resource('/csr_dashboards', App\Http\Controllers\Csr_dashboardController::class);



	Route::post('csr_dashboards/add_order', [App\Http\Controllers\Csr_dashboardController::class, 'add_order']);

	Route::get('csr_dashboards/edit_order/{id}', [App\Http\Controllers\Csr_dashboardController::class, 'edit_order']);

	Route::post('csr_dashboards/update_order', [App\Http\Controllers\Csr_dashboardController::class, 'update_order']);

	Route::get('csr_dashboards/show_order/{id}', [App\Http\Controllers\Csr_dashboardController::class, 'show_order']);

	Route::delete('csr_dashboards/delete_order/{id}', [App\Http\Controllers\Csr_dashboardController::class, 'delete_order']);



	Route::post('/fetch_reg_orders', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_reg_orders']);

	Route::post('/fetch_cancel_orders', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_cancel_orders']);

	Route::post('/fetch_hfq_orders', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_hfq_orders']);

	Route::post('/fetch_payment_orders', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_payment_orders']);

	Route::post('/fetch_summary_orders', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_summary_orders']);

	

	Route::post('/reschedule_reg_orders', [App\Http\Controllers\Csr_dashboardController::class, 'reschedule_reg_orders']);

	Route::post('/reschedule_cancel_orders', [App\Http\Controllers\Csr_dashboardController::class, 'reschedule_cancel_orders']);

	Route::post('/reschedule_hfq_orders', [App\Http\Controllers\Csr_dashboardController::class, 'reschedule_hfq_orders']);

	Route::post('/finalize_orders', [App\Http\Controllers\Csr_dashboardController::class, 'finalize_orders']);

	



	Route::post('/get_customer_lat_lng', [App\Http\Controllers\Csr_dashboardController::class, 'get_customer_lat_lng']);

	Route::post('/fetch_customer_details', [App\Http\Controllers\Csr_dashboardController::class, 'fetch_customer_details']);

	Route::post('csr_dashboards/schedule_payment_ride', [App\Http\Controllers\Csr_dashboardController::class, 'schedule_payment_ride']);





	Route::resource('/orders', App\Http\Controllers\OrderController::class);

	// Route::get('/order_list', [App\Http\Controllers\OrderController::class, 'list']);

	

	Route::post('orders/order_list', [App\Http\Controllers\OrderController::class, 'list']);



	Route::get('/orders/fetch_history/{id}',[App\Http\Controllers\OrderController::class, 'fetch_history']);

	Route::get('orders/send_invoice/{id}',[App\Http\Controllers\OrderController::class, 'send_invoice']);



	Route::delete('/orderDelete', [App\Http\Controllers\OrderController::class, 'destroy']);

	Route::post('/fetchCustomerDetail', [App\Http\Controllers\OrderController::class, 'fetchCustomerDetail']);

	Route::get('/assign_rider',[App\Http\Controllers\OrderController::class, 'assign_rider']);

	Route::post('/get_lat_lng', [App\Http\Controllers\OrderController::class, 'get_lat_lng']);



	

	Route::resource('/retainer_days', App\Http\Controllers\Customer_has_retainerController::class);

	Route::get('/retainer_list', [App\Http\Controllers\Customer_has_retainerController::class, 'list']);





	

	Route::get('/fetch_orders', [App\Http\Controllers\Csr_dashboardController::class, 'list']);

	// Route::get('/complaintList', [App\Http\Controllers\ComplaintController::class, 'list']);

	// Route::delete('/complaintDelete', [App\Http\Controllers\ComplaintController::class, 'destroy']);





	Route::resource('/complaints', App\Http\Controllers\ComplaintController::class);

	Route::post('complaints/complaint_list', [App\Http\Controllers\ComplaintController::class, 'list']);

	Route::delete('/complaint_delete', [App\Http\Controllers\ComplaintController::class, 'destroy']);



	Route::get('/complaints/complaint_add/{id}',[App\Http\Controllers\ComplaintController::class, 'complaint_add']);

	Route::get('/order_for_complaint_list', [App\Http\Controllers\ComplaintController::class, 'order_list']);

	Route::post('/fetch_complaint_tags', [App\Http\Controllers\ComplaintController::class, 'fetch_complaint_tags']);

	Route::get('/complaints/resolve_complaint/{id}',[App\Http\Controllers\ComplaintController::class, 'resolve_complaint']);

	Route::get('/complaints/trail_complaint/{id}',[App\Http\Controllers\ComplaintController::class, 'trail_complaint']);

	

	



	Route::resource('/order_details', App\Http\Controllers\Order_detailController::class);

	Route::get('/order_detail_list', [App\Http\Controllers\Order_detailController::class, 'list']);

	// Route::delete('/order_detail_delete', [App\Http\Controllers\Order_detailController::class, 'destroy']);

	Route::post('/fetch_items', [App\Http\Controllers\Order_detailController::class, 'fetch_items']);

	Route::post('/fetch_addons', [App\Http\Controllers\Order_detailController::class, 'fetch_addons']);

	Route::get('/order_details/create/{id}',[App\Http\Controllers\Order_detailController::class, 'create_order_detail']);

	Route::get('/order_details/fetch_history/{id}',[App\Http\Controllers\Order_detailController::class, 'fetch_history']);

	Route::get('/fn_move_to_hub',[App\Http\Controllers\Order_detailController::class, 'fn_move_to_hub']);

	

	Route::post('/verify_order', [App\Http\Controllers\Order_verifyController::class, 'verify_order']);

	Route::resource('/order_verifies', App\Http\Controllers\Order_verifyController::class);

	Route::get('/order_verify_list/{id}', [App\Http\Controllers\Order_verifyController::class, 'list']);

	Route::get('/order_verifies/show_tags/{id}',[App\Http\Controllers\Order_verifyController::class, 'show_tags']);

	

	

	Route::get('/order_verifies/special_verify/{id}',[App\Http\Controllers\Order_verifyController::class, 'special_verify']);

	Route::get('/order_verifies/special_show_tags/{id}',[App\Http\Controllers\Order_verifyController::class, 'special_show_tags']);

	Route::get('/order_verifies/fetch_history/{id}',[App\Http\Controllers\Order_verifyController::class, 'fetch_history']);





	Route::get('/add/{id}/{oid}',[App\Http\Controllers\Order_verifyController::class, 'find_n_assign_wash_house']);

	



	Route::resource('/wash_house_orders', App\Http\Controllers\Wash_house_has_orderController::class);

	Route::get('/wash_house_order_list/{id}', [App\Http\Controllers\Wash_house_has_orderController::class, 'list']);

	Route::post('/fetch_wash_house', [App\Http\Controllers\Wash_house_has_orderController::class, 'fetch_wash_house']);





	Route::resource('/wash_house_summaries', App\Http\Controllers\Wash_house_has_summaryController::class);

	Route::get('/wash_house_summary_list/{id}', [App\Http\Controllers\Wash_house_has_summaryController::class, 'list']);

	

	Route::resource('/order_inspects', App\Http\Controllers\Order_inspectController::class);

	Route::get('/order_inspect_list/{id}', [App\Http\Controllers\Order_inspectController::class, 'list']);
	Route::post('/order_inspect/create', [App\Http\Controllers\Order_inspectController::class, 'create'])->name('order_inspects.create');

	Route::post('/update_order_tags_status', [App\Http\Controllers\Order_inspectController::class, 'update_order_tags_status']);

	Route::post('/update_tags_status_HFQ', [App\Http\Controllers\Order_inspectController::class, 'update_tags_status_HFQ']);

	Route::get('/order_inspects/show_bags/{id}',[App\Http\Controllers\Order_inspectController::class, 'show_bags']);

	Route::get('/order_inspects/special_show_bags/{id}',[App\Http\Controllers\Order_inspectController::class, 'special_show_bags']);

	Route::get('/order_inspects/fetch_history/{id}',[App\Http\Controllers\Order_inspectController::class, 'fetch_history']);

	// Route::get('/order_inspects/special_inspect/{id}',[App\Http\Controllers\Order_inspectController::class, 'special_inspect']);



	Route::post('/inspect_order', [App\Http\Controllers\Order_inspectController::class, 'inspect_order']);

	

	Route::resource('/order_packs', App\Http\Controllers\Order_packController::class);

	Route::get('/order_packList', [App\Http\Controllers\Order_packController::class, 'list']);

	Route::get('/assign_delivery_rider',[App\Http\Controllers\Order_packController::class, 'assign_rider']);



	Route::resource('/order_hfqs', App\Http\Controllers\Order_hfqController::class);

	Route::get('/order_hfq_list/{id}', [App\Http\Controllers\Order_hfqController::class, 'list']);

	Route::get('/order_hfqs/show_bags/{id}',[App\Http\Controllers\Order_hfqController::class, 'show_bags']);

	Route::get('/order_hfqs/special_show_bags/{id}',[App\Http\Controllers\Order_hfqController::class, 'special_show_bags']);





	Route::resource('/route_plans', App\Http\Controllers\Route_planController::class);

	Route::post('/fetch_route_summary', [App\Http\Controllers\Route_planController::class, 'fetch_route_summary']);

	Route::post('/fetch_route_riders', [App\Http\Controllers\Route_planController::class, 'fetch_route_riders']);

	Route::post('/fetch_route_orders', [App\Http\Controllers\Route_planController::class, 'fetch_route_orders']);

	Route::post('route_plans/update_riders', [App\Http\Controllers\Route_planController::class, 'update_riders']);

	Route::post('route_plans/store_resort', [App\Http\Controllers\Route_planController::class, 'store_resort']);

	Route::post('route_plans/resort', [App\Http\Controllers\Route_planController::class, 'resort']);

	Route::get('/fetch_plan', [App\Http\Controllers\Route_planController::class, 'fetch_plan']);

	Route::post('route_plans/get_route', [App\Http\Controllers\Route_planController::class, 'get_route']);



	Route::resource('/scheduled_plans', App\Http\Controllers\Scheduled_planController::class);

	Route::post('/fetch_scheduled_plan', [App\Http\Controllers\Scheduled_planController::class, 'fetch_scheduled_plan']);

	Route::post('/fetch_payment_order', [App\Http\Controllers\Scheduled_planController::class, 'fetch_payment_order']);

	Route::post('/fetch_schedule_orders', [App\Http\Controllers\Scheduled_planController::class, 'fetch_schedule_orders']);

	Route::post('scheduled_plans/fetch_rider_plan', [App\Http\Controllers\Scheduled_planController::class, 'fetch_rider_plan']);



	Route::post('scheduled_plans/update_rider_plan', [App\Http\Controllers\Scheduled_planController::class, 'update_rider_plan']);

	Route::post('scheduled_plans/update_order_seq', [App\Http\Controllers\Scheduled_planController::class, 'update_order_seq']);



	Route::post('scheduled_plans/cancel_order', [App\Http\Controllers\Scheduled_planController::class, 'cancel_order']);

	Route::post('scheduled_plans/schedule_reg_orders', [App\Http\Controllers\Scheduled_planController::class, 'schedule_reg_orders']);

	Route::post('scheduled_plans/schedule_payment_rides', [App\Http\Controllers\Scheduled_planController::class, 'schedule_payment_rides']);

	Route::post('scheduled_plans/cancel_payment_rides', [App\Http\Controllers\Scheduled_planController::class, 'cancel_payment_rides']);

	 





	Route::resource('/reports', App\Http\Controllers\ReportController::class);

	Route::post('reports/report_list', [App\Http\Controllers\ReportController::class, 'list']);

	Route::post('/fetch_riders', [App\Http\Controllers\ReportController::class, 'fetch_riders']);

	Route::post('/update-start-reading', [App\Http\Controllers\ReportController::class, 'updateStartReading'])->name('update.start.reading');

	
	Route::resource('/fuel_report', App\Http\Controllers\Fuel_ReportController::class);

	Route::get('fuel_report/show/{id}', [App\Http\Controllers\Fuel_ReportController::class, 'show'])->name('fuel_reports_show');
	Route::post('fuel_report/lock', [App\Http\Controllers\Fuel_ReportController::class, 'toggleLock'])->name('fuel_reports_lock');
	Route::post('fuel_reports_check_lock', [App\Http\Controllers\Fuel_ReportController::class, 'checkLockStatus'])->name('fuel_reports_check_lock');
	Route::post('fuel_report/fuel_list', [App\Http\Controllers\Fuel_ReportController::class, 'list']);
	Route::post('/update_start_reading', [App\Http\Controllers\Fuel_ReportController::class, 'updateStartReading']);
		Route::post('/update_end_reading', [App\Http\Controllers\Fuel_ReportController::class, 'updateEndReading']);

	// Route::get('/route_planList', [App\Http\Controllers\Route_planController::class, 'list']);

	// Route::get('/cal_dis_time', [App\Http\Controllers\Route_planController::class, 'cal_dis_time']);

	// Route::post('/update_rider_profile', [App\Http\Controllers\Route_planController::class, 'update_rider_profile']);

	// Route::post('/create_route_plan', [App\Http\Controllers\Route_planController::class, 'create_route_plan']);





	Route::resource('/packings', App\Http\Controllers\PackingController::class);

	Route::post('packings/pack_list', [App\Http\Controllers\PackingController::class, 'list']);

	

	

	Route::resource('/taggings', App\Http\Controllers\TaggingController::class);

	Route::post('taggings/tag_list', [App\Http\Controllers\TaggingController::class, 'list']);

	Route::get('/taggings/fetch_history/{id}',[App\Http\Controllers\TaggingController::class, 'fetch_history']);

	

	Route::resource('/wh_dashboards', App\Http\Controllers\Wh_dashboardController::class);

	Route::post('wh_dashboards/wh_order_list/', [App\Http\Controllers\Wh_dashboardController::class, 'list']);



	Route::resource('/wh_billings', App\Http\Controllers\Wh_billingController::class);

	Route::post('wh_billings/wh_order_list/', [App\Http\Controllers\Wh_billingController::class, 'list']);


	Route::resource('/rider_incentives', App\Http\Controllers\RiderIncentivesController::class);
	Route::get('/rider_incentives_list', [App\Http\Controllers\RiderIncentivesController::class, 'list'])->name('rider_incentives_list');
	Route::post('/update_rider_incentives_status', [App\Http\Controllers\RiderIncentivesController::class, 'updateStatus'])->name('update_rider_incentives_status');
	Route::post('/update_rider_incentives_default', [App\Http\Controllers\RiderIncentivesController::class, 'updateDefault'])->name('update_rider_incentives_default');
	Route::delete('/rider_incentives_delete', [App\Http\Controllers\RiderIncentivesController::class, 'destroy'])->name('rider_incentives_delete');


	

});





