<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PullOutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProjectSiteController;
use App\Http\Controllers\TransferRequestController;
use App\Http\Controllers\MyToolsAndEquipmentController;


Route::view('/', 'login');

Route::middleware(['auth'])->group(function () {   
    // Route::prefix('/admin')->group(function () {
    //    Route::get('/test', function () { return 'test'; });
    // }); 
    Route::get('/dashboard_PM', function(){
        return view('pages.dashboard_PM');
    });
    Route::match(['get', 'post'], '/dashboard', function(){
        return view('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    // Route::view('/pages/warehouse', 'pages.warehouse');
    // Route::view('/pages/project_site', 'pages.project_site');
    Route::view('/pages/datatables', 'pages.datatables');
    Route::view('/pages/datatables', 'pages.datatables');
    Route::view('/pages/request_ongoing', 'pages.request_ongoing');
    Route::view('/pages/request_completed', 'pages.request_completed');
    // Route::view('/pages/my_te', 'pages.my_te');
    Route::view('/pages/pullout_ongoing', 'pages.pullout_ongoing');
    Route::view('/pages/rftte', 'pages.rftte');
    Route::view('/pages/rfteis_approved', 'pages.rfteis_approved');
    Route::view('/pages/pullout_warehouse', 'pages.pullout_warehouse');
    Route::view('/pages/rfteis', 'pages.rfteis');
    Route::view('/pages/barcode_scanner', 'pages.barcode_scanner');
    Route::view('/pages/rttte_acc', 'pages.rttte_acc');
    Route::view('/pages/daf', 'pages.daf');
    Route::view('/pages/site_to_site_transfer', 'pages.site_to_site_transfer');
    Route::view('/pages/pullout_completed', 'pages.pullout_completed');
    Route::view('/pages/approved_pullout', 'pages.approved_pullout');

    


    // Route::view('/pages/slick', 'pages.slick');
});


Route::controller(WarehouseController::class)->group(function () {
    Route::get('view_warehouse/{search?}/{desc?}', 'view_warehouse')->name('view_warehouse');
    Route::post('add_warehouse_tools', 'add_tools')->name('add_tools');
    Route::get('fetch_tools', 'fetch_tools')->name('fetch_tools');
    Route::post('edit_warehouse_tools', 'edit_tools')->name('edit_tools');
    Route::post('delete_warehouse_tools', 'delete_tools')->name('delete_tools');
    Route::post('request', 'request_tools')->name('request_tools');
});

Route::controller(ProjectSiteController::class)->group(function () {
    Route::get('project_site', 'view_project_site')->name('view_project_site');
    Route::get('fetch_tools_ps', 'fetch_tools_ps')->name('fetch_tools_ps');
    Route::post('ps_request', 'ps_request_tools')->name('ps_request_tools');
    Route::get('ps_teis_request', 'fetch_teis_request_ps')->name('fetch_teis_request_ps');
    Route::get('ps_teis_request_modal', 'ps_ongoing_teis_request_modal')->name('ps_ongoing_teis_request_modal');
    Route::post('add_price', 'add_price_acc')->name('add_price_acc');
    
});

Route::controller(TransferRequestController::class)->group(function () {
    Route::get('ongoing_teis_request', 'ongoing_teis_request')->name('ongoing_teis_request');
    Route::match(['GET', 'POST'], '/ongoing_teis_request_modal', 'ongoing_teis_request_modal')->name('ongoing_teis_request_modal');
    Route::get('teis_request', 'fetch_teis_request')->name('fetch_teis_request');
    Route::post('rfteis_approver', 'fetch_rfteis_approver')->name('fetch_rfteis_approver');
    Route::post('approve_tools', 'approve_tools')->name('approve_tools');
    Route::get('barcode_scanner', 'scanned_teis')->name('scanned_teis');
    Route::post('barcode_scanner_received', 'scanned_teis_received')->name('scanned_teis_received');
    Route::get('daf_approvers', 'fetch_daf_approver')->name('fetch_daf_approver');
    Route::post('daf_approve_tools', 'daf_approve_tools')->name('daf_approve_tools');
    Route::get('daf_table_modal', 'daf_table_modal')->name('daf_table_modal');
    Route::post('daf_add_price', 'add_price_acc_daf')->name('add_price_acc_daf');
    Route::get('site_tools', 'fetch_site_tools')->name('fetch_site_tools');
    Route::post('ps_approve_tools', 'ps_approve_tools')->name('ps_approve_tools');
    Route::post('ps_approve_rttte', 'ps_approve_rttte')->name('ps_approve_rttte');
    Route::get('completed_teis_request', 'completed_teis_request')->name('completed_teis_request');
    
    
});

Route::controller(MyToolsAndEquipmentController::class)->group(function () {
    Route::get('view_my_te', 'view_my_te')->name('view_my_te');
    Route::get('my_te', 'fetch_my_te')->name('fetch_my_te');
    Route::post('pullout_tools', 'pullout_request')->name('pullout_request');
    Route::post('add_state', 'add_state')->name('add_state');
});

Route::controller(PullOutController::class)->group(function () {
    Route::get('ongoing_pullout', 'fetch_ongoing_pullout')->name('fetch_ongoing_pullout');
    Route::get('ongoing_pullout_request', 'ongoing_pullout_request_modal')->name('ongoing_pullout_request_modal');
    Route::post('tobe_approve_tools', 'tobe_approve_tools')->name('tobe_approve_tools');
    Route::get('pullout_request', 'fetch_pullout_request')->name('fetch_pullout_request');
    Route::get('completed_pullout_request', 'fetch_completed_pullout')->name('fetch_completed_pullout');
    Route::get('fetch_sched_date', 'fetch_sched_date')->name('fetch_sched_date');
    Route::post('add_schedule', 'add_schedule')->name('add_schedule');
    Route::get('fetch_approved_pullout', 'fetch_approved_pullout')->name('fetch_approved_pullout');
    
});

// Route::controller(FileUploadController::class)->group(function(){
    
// });
Route::post('upload_process', [FileUploadController::class, 'upload_process'])->name('upload_process');
Route::post('ps_upload_process_ters', [FileUploadController::class, 'ps_upload_process_ters'])->name('ps_upload_process_ters');
Route::post('upload_process_ters', [FileUploadController::class, 'upload_process_ters'])->name('upload_process_ters');


Route::post('search', [SearchController::class, 'search'])->name('search');



Route::post('/', [UserController::class, 'auth_login'])->name('login');
Route::get('/logout', [UserController::class, 'auth_logout'])->name('logout');
