<?php

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
