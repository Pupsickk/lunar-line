<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ShopController,CartController,OrderController,AuthController};
Route::get('/',[ShopController::class,'home'])->name('home');
Route::get('/catalog',[ShopController::class,'catalog'])->name('catalog');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('product');
Route::get('/cart',[CartController::class,'index'])->name('cart');
Route::post('/cart/{product}',[CartController::class,'add'])->name('cart.add');
Route::patch('/cart/{product}',[CartController::class,'update'])->name('cart.update');
Route::get('/checkout',[OrderController::class,'checkout'])->name('checkout');
Route::post('/checkout',[OrderController::class,'store'])->middleware('throttle:10,1')->name('orders.store');
Route::get('/orders/{number}',[OrderController::class,'show'])->middleware('throttle:30,1')->name('orders.show');
Route::get('/login',[AuthController::class,'login'])->name('login');
Route::post('/login',[AuthController::class,'authenticate'])->middleware('throttle:5,1');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::get('/admin/orders',[OrderController::class,'admin'])->middleware(['auth','can:view-orders'])->name('admin.orders');
Route::view('/privacy','privacy')->name('privacy');
Route::view('/support','support')->name('support');
