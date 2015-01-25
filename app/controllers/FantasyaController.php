<?php

class FantasyaController extends BaseController {

    public function login($saved = null) {
        if (Request::isMethod('POST')) {
            Auth::attempt(array('name' => Input::get('user'), 'password' => Input::get('password')));
            return Redirect::to('/login');
        }
        return View::make('login', array('saved' => $saved));
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

    public function profile() {
        $user  = Auth::user();
        if ($user) {
            $email = Input::get('email');
            if ($email) {
                $user->email = $email;
                $user->save();
            }
            $password = Input::get('password');
            if ($password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        }
        return Redirect::to('/login/saved');
    }

    public function edit($what) {
        if ($what === 'news') {
            if (Request::isMethod('POST')) {
                $title   = Input::get('title');
                $content = Input::get('content');
                if ($title && $content) {
                    $article          = new News();
                    $article->title   = $title;
                    $article->content = $content;
                    $article->save();
                }
            }
            $news = DB::table(News::TABLE)->orderBy('id', 'DESC')->get();
            return View::make('edit-news', array('news' => $news));
        }
        return Redirect::to('/login');
    }

    public function delete($what, $id) {
        if ($what === 'news') {
            $article = News::find($id);
            if ($article) {
                $article->delete();
            }
            return Redirect::to('/edit/news#list');
        }
        return Redirect::to('/login');
    }

    public function news() {
        $news = DB::table(News::TABLE)->orderBy('id', 'DESC')->get();
        return View::make('news', array('news' => $news));
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

