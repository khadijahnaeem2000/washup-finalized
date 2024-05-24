<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RiderController;

use App\Http\Controllers\API\MeterController;

use App\Http\Controllers\API\OrderController;

use App\Http\Controllers\API\DashboardController;







Route::post("login",[RiderController::class, 'login']);

Route::post("forgot",[RiderController::class, 'forgot']);

// Route::get("reg_orders_rep/{rider_id}",[OrderController::class, 'fn_reg_orders_rep']);

// Route::get("payment_rides_repo/{rider_id}",[OrderController::class, 'fn_payment_rides_repo']);

Route::get("pickup/{rider_id}/{order_id}",[OrderController::class, 'fetch_pickup']);



Route::get("dropoff/{rider_id}/{order_id}",[OrderController::class, 'fetch_dropoff']);

 Route::post("payment",[OrderController::class, 'store_payment']);

Route::get("myrides/{rider_id}",[DashboardController::class, 'fetch_rides']);



Route::get("report/{rider_id}",[OrderController::class, 'fn_report']);

Route::get("noMoreOrder/{order_id}/{rider_id}",[OrderController::class, 'noMoreOrder']);
   Route::get("paymentonlyrides/{rider_id}",[OrderController::class, 'fetch_pay_rides']);

    Route::post("paymentonlyridessubmit",[OrderController::class, 'store_pay_rides']);

  Route::post("confirmpickup",[OrderController::class, 'confirm_pickup']);
Route::group(['middleware' => 'auth:sanctum'], function(){



    Route::get("fetch_items/{order_id}/{service_id}",[OrderController::class, 'fetch_items']);

    // Route::get("myrides/{rider_id}",[DashboardController::class, 'fetch_rides']);

    Route::post("move_to_hub",[DashboardController::class, 'move_to_hub']);

    Route::get("day_status/{rider_id}",[MeterController::class, 'day_status']);

    Route::get("get_delivery_date/{date}",[OrderController::class, 'get_delivery_date']);





    //All secure URL's

    Route::get("fetch_rider",[RiderController::class, 'fetch_rider']);

    Route::post("store_reading",[MeterController::class, 'store_reading']);



    

    Route::get("dashboard/{rider_id}",[DashboardController::class, 'fetch_dashboard']);

    Route::get("riderhistory/{rider_id}/{year}/{month}",[DashboardController::class, 'fetch_rider_history']);

    // Route::get("myrides/{rider_id}",[DashboardController::class, 'fetch_rides']);





    Route::get("recent_orders/{rider_id}",[DashboardController::class, 'fetch_recently_added_orders']);

    

    //Route::get("pickup/{rider_id}/{order_id}",[OrderController::class, 'fetch_pickup']);

    // Route::get("fetch_items/{order_id}/{service_id}",[OrderController::class, 'fetch_items']);



    Route::post("store_items",[OrderController::class, 'store_items']);



   



    

    



    // Route::get("dropoff/{rider_id}/{order_id}",[OrderController::class, 'fetch_dropoff']);

    Route::get("pickdrop/{rider_id}/{order_id}",[OrderController::class, 'fetch_pickdrop']); 

  
    
    Route::get("addanotherorder/{rider_id}/{customer_id}",[OrderController::class, 'store_new_order']);



    Route::get("cancel",[OrderController::class, 'get_reasons']);

    Route::post("cancel",[OrderController::class, 'cancel_order']);



    Route::post("check",[OrderController::class, 'check']);



 


    

    Route::post("logout",[RiderController::class, 'logout']);

});







