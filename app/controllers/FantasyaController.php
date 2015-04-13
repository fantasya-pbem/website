<?php

class FantasyaController extends BaseController {

	public function login($saved = null) {
		if (Request::isMethod('POST')) {
			Auth::attempt(array('name' => Input::get('user'), 'password' => Input::get('password')));
			return Redirect::to('/login');
		}
		$flags = array();
		if (User::has(User::CAN_CREATE_NEWS)) {
			$flags[] = 'News verfassen';
		}
		if (User::has(User::CAN_BETA_TEST)) {
			$flags[] = 'Beta-Tester';
		}
		$parties = Auth::user() ? Party::allFor(Auth::user()) : array();
		return View::make('login', array('flags' => $flags, 'games' => Game::allById(), 'parties' => $parties, 'saved' => $saved));
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

	public function change($what) {
		//zur Zeit nur $what = world
		$current = Session::get('game');
		$parties = Party::allFor(Auth::user());
		$games   = array();
		foreach ($parties as $game => $parties) {
			if (count($parties) > 0) {
				$games[] = $game;
			}
		}
		$n = count($games);
		if ($n > 0) {
			$next = 0;
			for ($i = 0; $i < $n; $i++) {
				$game = $games[$i];
				if ($game === $current) {
					$next = $i + 1;
					break;
				}
			}
			if ($next >= $n) {
				$next = 0;
			}
			Session::put('game', $games[$next]);
			return Redirect::to('orders');
		}
	}

	public function orders($party = null) {
		$game    = Game::current();
		$parties = Party::allFor(Auth::user());
		$parties = $parties[$game->id];
		if (Request::isMethod('POST')) {
			$party = Input::get('party');
			$turn  = Input::get('turn');
		} else {
			$turn = Settings::on($game->database)->find('game.runde')->Value;
		}
		$party = $party ? $parties[$party] : current($parties);
		$order = new Order($game, $party, $turn);
		$p     = array();
		foreach ($parties as $id => $pty) {
			$p[$id] = $pty->name;
		}
		$turns = array();
		$next  = $turn - 5;
		$last  = $turn + 5;
		while ($next <= $last ) {
			$turns[$next++] = $next;
		}
		return View::make('orders', array('turn'  => $turn,  'turns'   => $turns,
										  'party' => $party, 'parties' => $p,
										  'orders' => $order->getOrders(),));
	}

	public function send($what) {
		//zur Zeit nur $what = orders
		$game    = Game::current();
		$party   = Party::allFor(Auth::user());
		$parties = $party[$game->id];
		if (Request::isMethod('POST')) {
			$p      = Input::get('party');
			$turn   = Input::get('turn');
			$orders = Input::get('orders');
			if ($p && $orders) {
				$order = new Order($game, $parties[$p], $turn);
				$order->setOrders($orders);
				return Redirect::to('/orders/' . $p);
			}
		}
		$p = array();
		foreach ($parties as $id => $party) {
			$p[$id] = $party->name;
		}
		$turn  = Settings::on($game->database)->find('game.runde')->Value;
		$turns = array();
		$next  = $turn;
		$last  = $turn + 4;
		while ($next <= $last ) {
			$turns[$next++] = $next;
		}
		return View::make('send-orders', array('parties' => $p, 'turns' => $turns));
	}

	public function edit($what) {
		//zur Zeit nur $what = news
		if ($what === 'news') {
			if (Request::isMethod('POST')) {
				$title   = Input::get('title');
				$content = Input::get('content');
				if ($title && $content) {
					$article          = new News();
					$article->title   = $title;
					$article->content = nl2br($content);
					$article->save();
				}
				return Redirect::to('edit/news#list');
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

	public function myth() {
		if (Request::isMethod('POST')) {
			$mythtext  = Input::get('mythtext');
			$rules     =  array('mythtext' => 'required|min:10|max:140', 'captcha' => array('required', 'captcha'));
			$validator = Validator::make(Input::all(), $rules);
			if ($validator->passes()) {
				$myth       = new Myth();
				$myth->myth = $mythtext;
				$myth->save();
				return Redirect::to('/myths');
			}
		}
		return View::make('myth', array('mythtext' => Input::get('mythtext')));
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

