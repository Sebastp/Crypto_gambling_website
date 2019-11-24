<?php


Route::get('/', 'mainController@showHome')->name('home')->middleware('CheckBalance');
// Route::get('/wallet', 'mainController@showWallet');

Route::get('/admin_prf', 'mainController@show_profits');

Route::middleware(['userInfo'])->group(function () {

  Route::get('/predict', 'gamesController@showGame1')->name('game1')->middleware('CheckBalance');
  Route::post('/predict/info', 'gamesController@getParameter')->name('game1_getParameter')->middleware('LogUserActivity');
  Route::post('/predict/bet', 'gamesController@saveBet');
  Route::post('/predict/last_bet', 'gamesController@betResult');

  Route::get('/profile', 'accountsController@showProfileDashboard')->name('profile')->middleware('CheckBalance');
  Route::post('/profile/edit', 'accountsController@edit_prof')->name('profile_upd');
  Route::post('/withdraw', 'accountsController@withdraw')->name('transactio_w');
});


Route::post('/login', 'accountsController@login')->name('login');
Route::get('/register/{redirectNext?}', 'accountsController@generateNew')->name('register');
