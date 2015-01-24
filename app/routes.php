<?php

Route::get('/', function() {
	return Redirect::route('index');
});

Route::get('index', array('as' => 'index', function() {
	return Redirect::route('news');
}));

Route::get('news', array('as' => 'news', function()
{
	return View::make('news');
}));

Route::get('about', function() {
	return View::make('about');
});

Route::get('myths', 'FantasyaController@myths');

Route::get('contact', function() {
	return View::make('contact');
});

Route::match(array('GET', 'POST'), 'login', 'FantasyaController@login');

Route::get('logout', function() {
    Auth::logout();
    return View::make('logout');
});

Route::match(array('GET', 'POST'), 'reset', 'FantasyaController@reset');

Route::get('world/{id?}', 'FantasyaController@world');

