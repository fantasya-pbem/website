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

Route::get('donate', function() {
	return View::make('donate');
});

Route::get('contact', function() {
	return View::make('contact');
});

Route::match(array('GET', 'POST'), 'register', 'FantasyaController@register');

Route::get('registered', function() {
	return View::make('registered');
});

Route::match(array('GET', 'POST'), 'login/{saved?}', 'FantasyaController@login');

Route::get('change/{what}', array('before' => 'auth', 'uses' => 'FantasyaController@change'));

Route::match(array('GET', 'POST'), 'enter', array('before' => 'auth', 'uses' => 'FantasyaController@enter'));

Route::get('revoke/{world}/{party}', array('before' => 'auth', 'uses' => 'FantasyaController@revoke'));

Route::match(array('GET', 'POST'), 'orders/{party?}', array('before' => 'auth', 'uses' => 'FantasyaController@orders'));

Route::match(array('GET', 'POST'), 'send/{what}', array('before' => 'auth', 'uses' => 'FantasyaController@send'));

Route::get('secure', function() {
    $mail = isset($_SERVER['SSL_CLIENT_S_DN_Email']) ? $_SERVER['SSL_CLIENT_S_DN_Email']
          : (isset($_SERVER['REDIRECT_SSL_CLIENT_S_DN_Email']) ? $_SERVER['REDIRECT_SSL_CLIENT_S_DN_Email'] : null);
    if ($mail) {
        $user = User::where('email', '=', $mail)->first();
        if ($user) {
            Auth::login($user);
        }
    }
    return Redirect::to('/login');
});

Route::get('logout', array('before' => 'auth', function() {
	Auth::logout();
	Session::flush();
	return View::make('logout');
}));

Route::match(array('GET', 'POST'), 'reset', 'FantasyaController@reset');

Route::post('profile', array('before' => 'auth', 'uses' => 'FantasyaController@profile'));

Route::match(array('GET', 'POST'), 'edit/{what}', array('before' => 'auth', 'uses' => 'FantasyaController@edit'));

Route::get('delete/{what}/{id}', array('before' => 'auth', 'uses' => 'FantasyaController@delete'));

Route::get('world/{id?}', 'FantasyaController@world');
