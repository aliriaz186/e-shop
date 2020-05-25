<?php

/*
|--------------------------------------------------------------------------
| Offline Payment Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::resource('manual_payment_methods','ManualPaymentMethodController');
    Route::get('/manual_payment_methods/destroy/{id}', 'ManualPaymentMethodController@destroy')->name('manual_payment_methods.destroy');
});

//FrontEnd
Route::post('/purchase_history/make_payment', 'ManualPaymentMethodController@show_payment_modal')->name('checkout.make_payment');
Route::post('/purchase_history/make_payment/submit', 'ManualPaymentMethodController@submit_offline_payment')->name('purchase_history.make_payment');
