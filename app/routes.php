<?php

Route::get('/', function() {
	return Redirect::route('index');
});

Route::get('index', array('as' => 'index', function() {
	return Redirect::route('news');
}));

Route::get('news', 'FantasyaController@news');

Route::get('about', function() {
	return View::make('about');
});

Route::get('myths', 'FantasyaController@myths');

Route::get('contact', function() {
	return View::make('contact');
});

Route::match(array('GET', 'POST'), 'login/{saved?}', 'FantasyaController@login');

Route::get('logout', function() {
    Auth::logout();
    return View::make('logout');
});

Route::match(array('GET', 'POST'), 'reset', 'FantasyaController@reset');

Route::post('profile', 'FantasyaController@profile');

Route::match(array('GET', 'POST'), 'edit/{what}', 'FantasyaController@edit');

Route::get('delete/{what}/{id}', 'FantasyaController@delete');

Route::get('world/{id?}', 'FantasyaController@world');

