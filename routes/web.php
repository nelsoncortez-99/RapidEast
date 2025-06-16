<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

// Cargar controladores
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetalleOrdenController;



// Autenticación (solo una llamada)
Auth::routes(['register' => true]);

// Ruta home
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de recursos
Route::resource('/category', CategoriaController::class);
Route::resource('/client', ClienteController::class);
Route::resource('/employee', EmpleadoController::class);
Route::resource('/state', EstadoController::class);
Route::resource('/menu', MenuController::class);
Route::resource('/mpayment', MetodoPagoController::class);
Route::resource('/role', RolController::class);
Route::resource('/order', OrdenController::class);
Route::resource('/orderDetails', DetalleOrdenController::class);


// Ruta para DataTables de usuarios
    Route::get('/user/data', [UserController::class, 'show'])->name('user.data');
    Route::resource('user', UserController::class)->except(['show']);

// Rutas específicas para órdenes
Route::get('/order/data', [OrdenController::class, 'data'])->name('order.data');
Route::get('/order/{order}/details', [OrdenController::class, 'getDetails'])->name('order.details');