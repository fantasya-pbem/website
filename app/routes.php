<?php

Route::get('/', function() {
	return Redirect::route('index');
});

Route::get('index', array('as' => 'index', function() {
	return Redirect::route('news');
}));

Route::get('news', array('as' => 'news', 'uses' => 'FantasyaController@news'));

Route::get('about', function() {
	return View::make('about');
});

Route::get('myths', 'FantasyaController@myths');

Route::match(array('GET', 'POST'), 'myth', 'FantasyaController@myth');

Route::get('contact', function() {
	return View::make('contact');
});

Route::match(array('GET', 'POST'), 'login/{saved?}', 'FantasyaController@login');

Route::get('change/{what}', 'FantasyaController@change');

Route::match(array('GET', 'POST'), 'orders/{party?}', 'FantasyaController@orders');

Route::match(array('GET', 'POST'), 'send/{what}', 'FantasyaController@send');

Route::get('logout', function() {
    Auth::logout();
    return View::make('logout');
});

Route::match(array('GET', 'POST'), 'reset', 'FantasyaController@reset');

Route::post('profile', 'FantasyaController@profile');

Route::match(array('GET', 'POST'), 'edit/{what}', 'FantasyaController@edit');

Route::get('delete/{what}/{id}', 'FantasyaController@delete');

Route::get('world/{id?}', 'FantasyaController@world');

