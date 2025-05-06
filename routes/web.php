<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PullOutController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ViewFormsController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProjectSiteController;
use App\Http\Middleware\ActionLoggerMiddleware;
use App\Http\Controllers\TransferRequestController;
use App\Http\Controllers\MyToolsAndEquipmentController;




Route::get('/rttte_view', function(){
    return view('pages.view_rttte');
});

Route::get('pullout_view', function(){
    return view('pages.view_pullout');
});

Route::view('/', 'login');

// Route::fallback(function () {
//     return redirect()->route('login');
// });




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

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(ActionLoggerMiddleware::class);

    // Route::view('/pages/warehouse', 'pages.warehouse');
    // Route::view('/pages/project_site', 'pages.project_site');
    // Route::view('/pages/approvers_setup', 'pages.approvers_setup');
    Route::view('/pages/request_ongoing', 'pages.request_ongoing')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/request_for_receiving', 'pages.request_for_receiving')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/ps_request_for_receiving', 'pages.ps_request_for_receiving')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/request_completed', 'pages.request_completed')->middleware(ActionLoggerMiddleware::class);
    // Route::view('/pages/my_te', 'pages.my_te')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/pullout_ongoing', 'pages.pullout_ongoing')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rftte', 'pages.rftte')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rftte_completed', 'pages.rftte_completed')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rfteis_approved', 'pages.rfteis_approved')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/pullout_warehouse', 'pages.pullout_warehouse')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rfteis', 'pages.rfteis')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/barcode_scanner', 'pages.barcode_scanner')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rttte_acc', 'pages.rttte_acc')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rfteis_acc', 'pages.rfteis_acc')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/daf', 'pages.daf')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/site_to_site_transfer', 'pages.site_to_site_transfer')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/pullout_completed', 'pages.pullout_completed')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/approved_pullout', 'pages.approved_pullout')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/pullout_for_receiving', 'pages.pullout_for_receiving')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/site_to_site_approved', 'pages.site_to_site_approved')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/sts_request_completed', 'pages.sts_request_completed')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/users_management', 'pages.users_management');
    Route::view('/pages/rftte_signed_form_proof', 'pages.rftte_signed_form_proof')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/not_serve_items', 'pages.not_serve_items')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/report_pe_logs', 'pages.report_pe_logs')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/report_te_logs', 'pages.report_te_logs')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/rfteis_disapproved', 'pages.rfteis_disapproved')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/tool_extension_request', 'pages.tool_extension_request')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/acc_approved_request', 'pages.acc_approved_request')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/list_of_requests', 'pages.list_of_requests')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/upload_tools', 'pages.upload_tools')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/list_of_upload_tools', 'pages.list_of_upload_tools')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/view_logs', 'pages.view_logs');



    Route::view('/pages/qrcode_scanner', 'pages.qrcode_scanner')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/qrcode_generator', 'pages.qrcode_generator')->middleware(ActionLoggerMiddleware::class);


    Route::view('/pages/pullout_receiving', 'pages.pullout_receiving')->middleware(ActionLoggerMiddleware::class);
    Route::view('/pages/pullout_completed_warehouse', 'pages.pullout_completed_warehouse')->middleware(ActionLoggerMiddleware::class);

    Route::controller(WarehouseController::class)->group(function () {
        Route::get('view_warehouse/{search?}/{desc?}', 'view_warehouse')->name('view_warehouse')->middleware(ActionLoggerMiddleware::class);
        Route::post('add_warehouse_tools', 'add_tools')->name('add_tools');
        Route::get('fetch_tools', 'fetch_tools')->name('fetch_tools');
        Route::post('edit_warehouse_tools', 'edit_tools')->name('edit_tools');
        Route::post('delete_warehouse_tools', 'delete_tools')->name('delete_tools');
        Route::post('request', 'request_tools')->name('request_tools');
    });

    Route::controller(ProjectSiteController::class)->group(function () {
        Route::get('view_project_site/{search?}/{desc?}', 'view_project_site')->name('view_project_site')->middleware(ActionLoggerMiddleware::class);
        Route::get('fetch_tools_ps', 'fetch_tools_ps')->name('fetch_tools_ps');
        Route::post('ps_request', 'ps_request_tools')->name('ps_request_tools');
        Route::get('ps_teis_request', 'fetch_teis_request_ps')->name('fetch_teis_request_ps');
        Route::get('ps_teis_request_modal', 'ps_ongoing_teis_request_modal')->name('ps_ongoing_teis_request_modal');
        Route::post('add_price', 'add_price_acc')->name('add_price_acc');

    });

    Route::controller(TransferRequestController::class)->group(function () {
        Route::get('ongoing_teis_request', 'ongoing_teis_request')->name('ongoing_teis_request');
        Route::get('ps_ongoing_teis_request', 'ps_ongoing_teis_request')->name('ps_ongoing_teis_request');
        Route::match(['GET', 'POST'], '/ongoing_teis_request_modal', 'ongoing_teis_request_modal')->name('ongoing_teis_request_modal');
        Route::get('teis_request', 'fetch_teis_request')->name('fetch_teis_request');
        Route::get('teis_request_completed', 'fetch_teis_request_completed')->name('fetch_teis_request_completed');
        Route::post('rfteis_approver', 'fetch_rfteis_approver')->name('fetch_rfteis_approver');
        Route::post('approve_tools', 'approve_tools')->name('approve_tools');
        Route::get('barcode_scanner', 'scanned_teis')->name('scanned_teis');
        Route::post('barcode_scanner_received', 'scanned_teis_received')->name('scanned_teis_received');
        Route::post('teis_not_received', 'teis_not_received')->name('teis_not_received');
        Route::get('daf_approvers', 'fetch_daf_approver')->name('fetch_daf_approver');
        Route::post('daf_approve_tools', 'daf_approve_tools')->name('daf_approve_tools');
        Route::get('daf_table_modal', 'daf_table_modal')->name('daf_table_modal');
        Route::post('daf_add_price', 'add_price_acc_daf')->name('add_price_acc_daf');
        Route::get('site_tools', 'fetch_site_tools')->name('fetch_site_tools');
        Route::post('ps_approve_tools', 'ps_approve_tools')->name('ps_approve_tools');
        Route::post('ps_approve_rttte', 'ps_approve_rttte')->name('ps_approve_rttte');
        Route::get('completed_teis_request', 'completed_teis_request')->name('completed_teis_request');
        Route::post('tools_deliver', 'tools_deliver')->name('tools_deliver');
        Route::post('track_request', 'track_request')->name('track_request');
        Route::get('completed_sts_request', 'completed_sts_request')->name('completed_sts_request');
        Route::get('sts_request_approved', 'sts_request_approved')->name('sts_request_approved');
        Route::get('fetch_teis_request_acc', 'fetch_teis_request_acc')->name('fetch_teis_request_acc');
        Route::post('rfteis_acc_proceed', 'rfteis_acc_proceed')->name('rfteis_acc_proceed');

        Route::post('approve_daf', 'approve_daf')->name('approve_daf');

        Route::post('remove_tool', 'remove_tool')->name('remove_tool');

        Route::post('redelivery_status', 'redelivery_status')->name('redelivery_status');

        Route::get('project_tagging', 'project_tagging')->name('project_tagging')->middleware(ActionLoggerMiddleware::class);
        Route::get('fetch_assigned_personnel', 'fetch_assigned_personnel')->name('fetch_assigned_personnel');
        Route::post('assign_personnel', 'assign_personnel')->name('assign_personnel');
        Route::post('delete_personnel', 'delete_personnel')->name('delete_personnel');


        Route::post('disapprove_request', 'disapprove_request')->name('disapprove_request');
        Route::get('acc_approved_request', 'acc_approved_request')->name('acc_approved_request');

        Route::post('cancel_request', 'cancel_request')->name('cancel_request');

    });

    Route::controller(MyToolsAndEquipmentController::class)->group(function () {
        Route::get('view_my_te', 'view_my_te')->name('view_my_te')->middleware(ActionLoggerMiddleware::class);;
        Route::get('my_te', 'fetch_my_te')->name('fetch_my_te');
        Route::post('pullout_tools', 'pullout_request')->name('pullout_request');
        Route::post('add_state', 'add_state')->name('add_state');
        Route::post('request_for_extension', 'request_for_extension')->name('request_for_extension');

        Route::get('fetch_request_for_extension', 'fetch_request_for_extension')->name('fetch_request_for_extension');
        Route::post('approve_extension_tool', 'approve_extension_tool')->name('approve_extension_tool');
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
        Route::post('received_pullout_tools', 'received_pullout_tools')->name('received_pullout_tools');
        Route::post('fetch_current_site', 'fetch_current_site')->name('fetch_current_site');
        Route::post('pullout_not_received', 'pullout_not_received')->name('pullout_not_received');

    });

    Route::controller(AdminController::class)->group(function(){
        Route::get('approvers_setup','approvers_setup')->name('approvers_setup');
        Route::get('fetch_approvers','fetch_approvers')->name('fetch_approvers');
        Route::post('fetch_users','fetch_users')->name('fetch_users');
        Route::post('add_approvers','add_approvers')->name('add_approvers');
        Route::post('edit_approver','edit_approver')->name('edit_approver');
        Route::post('delete_approver','delete_approver')->name('delete_approver');
        Route::post('user_per_area','user_per_area')->name('user_per_area');
        Route::post('update_sequence','update_sequence')->name('update_sequence');
        Route::post('add_zero_sequence','add_zero_sequence')->name('add_zero_sequence');
        Route::get('fetch_users_admin','fetch_users_admin')->name('fetch_users_admin');
        Route::post('user_add_edit','user_add_edit')->name('user_add_edit');
        Route::post('change_status','change_status')->name('change_status');
    });

    Route::controller(ViewFormsController::class)->group(function(){
        Route::get('view_transfer_request','view_transfer_request')->name('view_transfer_request');
        Route::get('view_pullout_request','view_pullout_request')->name('view_pullout_request');
        Route::get('rfteis_approvers_view','rfteis_approvers_view')->name('rfteis_approvers_view');
        Route::get('rttte_approvers_view','rttte_approvers_view')->name('rttte_approvers_view');
    });


    Route::controller(ReportsController::class)->group(function(){
        Route::get('report_pe_logs', 'report_pe_logs')->name('report_pe_logs');
        Route::get('report_te_logs', 'report_te_logs')->name('report_te_logs');

        // viewer
        Route::get('request_list', 'request_list')->name('request_list');
        //log
        Route::get('fetch_logs', 'fetch_logs')->name('fetch_logs');
    });

    // Route::controller(FileUploadController::class)->group(function(){

    // });
    Route::post('upload_process', [FileUploadController::class, 'upload_process'])->name('upload_process');
    Route::post('ps_upload_process_ters', [FileUploadController::class, 'ps_upload_process_ters'])->name('ps_upload_process_ters');
    Route::post('upload_process_ters', [FileUploadController::class, 'upload_process_ters'])->name('upload_process_ters');
    Route::post('upload_tools_pic', [FileUploadController::class, 'upload_tools_pic'])->name('upload_tools_pic');
    Route::post('upload_proof_of_receiving', [FileUploadController::class, 'upload_proof_of_receiving'])->name('upload_proof_of_receiving');
    Route::post('upload_photo_for_pullout', [FileUploadController::class, 'upload_photo_for_pullout'])->name('upload_photo_for_pullout');


    Route::post('search', [SearchController::class, 'search'])->name('search');


    Route::post('import_preview', [ImportController::class, 'previewImport'])->name('import_preview');
    Route::post('import_excel', [ImportController::class, 'confirmImport'])->name('import_excel');
    Route::get('fetch_upload_tools', [ImportController::class, 'fetch_upload_tools'])->name('fetch_upload_tools');
    Route::post('import_tools_details', [ImportController::class, 'import_tools_details'])->name('import_tools_details');
    Route::post('import_tool_add_price', [ImportController::class, 'import_tool_add_price'])->name('import_tool_add_price');

    Route::get('/download_excel_template', [ImportController::class, 'downloadTemplate'])->name('download_excel_template');
});

// para sa checking daily kung meron na pa expired na tools
Route::get('daily', [MyToolsAndEquipmentController::class, 'daily'])->name('daily');


Route::post('/', [UserController::class, 'auth_login'])->name('login');
Route::post('/logout', [UserController::class, 'auth_logout'])->name('logout');

Route::middleware(['verify.api.token'])->post('/login-via-oms', [UserController::class, 'loginViaOMS']);
Route::get('/login-with-token', [UserController::class, 'authViaOMS'])->name('login.with.token');
