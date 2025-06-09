<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//cargar controladores
use  App\Http\Controllers\CategoriaController;
use  App\Http\Controllers\ClienteController;
use  App\Http\Controllers\EmpleadoController;
use  App\Http\Controllers\EstadoController;
use  App\Http\Controllers\MenuController;
use  App\Http\Controllers\MetodoPagoController;
use  App\Http\Controllers\OrdenController;
use  App\Http\Controllers\RolController;

Route::get('/home', function () {
    return view('home');
})->middleware('auth');

Route::resource('/category', CategoriaController::class);
Route::resource('/client', ClienteController::class);
Route::resource('/employee', EmpleadoController::class);
Route::resource('/state', EstadoController::class);
Route::resource('/menu', MenuController::class);
Route::resource('/mpayment', MetodoPagoController::class);
Route::resource('/order', OrdenController::class);
Route::resource('/role', RolController::class);

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
