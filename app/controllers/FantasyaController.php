<?php

class FantasyaController extends BaseController {

    public function login() {
        if (Request::isMethod('POST')) {
            Auth::attempt(array('name' => Input::get('user'), 'password' => Input::get('password')));
            return Redirect::to('/login');
        }
        return View::make('login');
    }

    public function reset() {
        $success = null;
        if (Request::isMethod('POST')) {
            $success = false;
            $user    = User::where('name', '=', Input::get('user'))->where('email', '=', Input::get('email'))->first();
            if ($user) {
                $password       = uniqid();
                $user->password = Hash::make($password);
                $user->save();
                Mail::send('reset-mail', array('user' => $user->name, 'password' => $password), function($message) use ($user) {
                    $message->from('admin@fantasya-pbem.de', 'Fantasya-Administrator');
                    $message->to($user->email);
                    $message->subject('Fantasya-Passwort-Reset');
                });
                $success = true;
            }
        }
        return View::make('reset', array('success' => $success));
    }
    
    public function myths() {
        //$myths = Myth::all();
        $myths = DB::table(Myth::TABLE)->orderBy('id', 'DESC')->get();
        return View::make('myths', array('myths' => $myths));
    }
    
    public function world($id = null) {
        if (!$id) {
            $id = (int)DB::table(Game::TABLE)->limit(1)->pluck('id');
        }
        $game = Game::find($id);
        $turn = Settings::on($game->database)->find('game.runde');
        return View::make('world', array('game' => $game, 'turn' => $turn->Value));
    }
    
}

