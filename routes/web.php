<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;


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
    Route::view('/pages/warehouse', 'pages.warehouse');
    Route::view('/pages/project_site', 'pages.project_site');
    Route::view('/pages/datatables', 'pages.datatables');
    Route::view('/pages/datatables', 'pages.datatables');
    Route::view('/pages/request_ongoing', 'pages.request_ongoing');
    Route::view('/pages/request_completed', 'pages.request_completed');
    // Route::view('/pages/slick', 'pages.slick');
});


Route::controller(WarehouseController::class)->group(function () {
    Route::post('add_warehouse_tools', 'add_tools')->name('add_tools');
    Route::get('fetch_tools', 'fetch_tools')->name('fetch_tools');
    Route::post('edit_warehouse_tools', 'edit_tools')->name('edit_tools');
    Route::post('delete_warehouse_tools', 'delete_tools')->name('delete_tools');
});

Route::post('/', [UserController::class, 'auth_login'])->name('login');
Route::get('/logout', [UserController::class, 'auth_logout'])->name('logout');
