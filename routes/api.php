<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminMedicinController;
use App\Http\Controllers\UsersMedicinController;
use App\Http\Controllers\MedicineController;

//********//
Route::post('register',[AuthController::class,'register']);##
Route::post('login',[AuthController::class,'login']);##
//********//

Route::group(['middleware'=>['auth:api','check']],function(){
Route::post('show_by_category',[MedicineController::class,'show_by_category']);##
Route::post('search',[MedicineController::class,'search']);##
Route::post('show_details',[MedicineController::class,'show_details']);##
Route::get('get_all_categories',[MedicineController::class,'get_all_categories']);##
Route::post('user_be_order',[UsersMedicinController::class,'be_order']);##
Route::get('user_show_order',[UsersMedicinController::class,'show_order']);##
Route::post('user_select_show_order',[UsersMedicinController::class,'select_show_order']);##
Route::post('add_favourite',[UsersMedicinController::class,'add_favourite']);##
Route::get('get_favourite',[UsersMedicinController::class,'get_favourite']);##
Route::post('delete_favourite',[UsersMedicinController::class,'delete_favourite']);##
//المستخدم يرجع الطلبيات تبعو باخر فترة
Route::get('user_show_orders_report',[ReportController::class,'user_show_orders_report']);##
//user_show_orders_value_report المستخدم قيم الفواتير تبعو
Route::get('user_show_orders_value_report',[ReportController::class,'user_show_orders_value_report']);##
//المستخدم يرجع قيمة أكبر فاتورة واقل فاتورة
Route::get('user_show_max_min_orders_value_report',[ReportController::class,'user_show_max_min_orders_value_report']);
});
//*********//

//*********//
Route::group(['middleware'=>['auth:api','admin']],function(){
//اضافة دوا
Route::post('add',[AdminMedicinController::class,'admin_add_medicine']);##
//show_order
Route::post('admin_show_order',[AdminMedicinController::class,'show_order']);##
//admin_sent_order
Route::post('admin_sent_order',[AdminMedicinController::class,'admin_sent_order']);##
//استلام الطلبية
Route::post('admin_received_order',[AdminMedicinController::class,'admin_received_order']);##
//الغاء الطلبية
Route::post('admin_cancel_order',[AdminMedicinController::class,'admin_cancel_order']);##
//دفع الطلبية
Route::post('admin_paid_order',[AdminMedicinController::class,'admin_paid_order']);##
//الادمن يشوف الادوية المضافة حديثا
Route::post('admin_show_medicines_report',[ReportController::class,'admin_show_medicines_report']);##
//الادمن يشوف الطلبات المضافو حديثا
Route::post ('admin_show_orders_report',[ReportController::class,'admin_show_orders_report']);##
///مجموع الفواتير
Route::post('admin_show_orders_value_report',[ReportController::class,'admin_show_orders_value_report']);##
//يعرض الادوية المنتهية صلاحيتا
Route::post('admin_end_date',[ReportController::class,'expired_drugs']);##
// 
Route::post('admin_show_losses',[ReportController::class,'losses']);##
});
//*********//







Route::group(['middleware'=>['auth:api']],function(){
    Route::post('logout',[AuthController::class,'logout']);

});

