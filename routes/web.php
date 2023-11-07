<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// All Normal Users Routes List
Route::middleware(['auth','user-access:user'])->group(function(){
    Route::get('/home',[HomeController::class,'index'])->name('home');
});

// All Admin Users Route List
Route::middleware(['auth','user-access:admin'])->group(function(){
    Route::get('/admin/home',[HomeController::class,'adminHome'])->name('admin.home');
});

// All Manager Route List

Route::middleware(['auth','user-access:manager'])->group(function(){
    Route::get('/manager/home',[HomeController::class,'managerHome'])->name('manager.home');
});
