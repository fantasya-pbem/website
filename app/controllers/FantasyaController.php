<?php

class FantasyaController extends BaseController {

	public function login($saved = null) {
		if (Request::isMethod('POST')) {
			if (Auth::attempt(array('name' => Input::get('user'), 'password' => Input::get('password')))) {
				// MD5-Passwort in die Parteien schreiben (Workaround für alte Beta-Test).
				$user = Auth::user();
				$md5  = $user->passwordmd5;
				if (strlen($md5) > 0) {
					foreach (Party::allFor($user) as $parties) {
						foreach ($parties as $party) {
							if ($party->password !== $md5) {
								$party->password = $md5;
								$party->save();
							}
						}
					}
				}
			}
			return Redirect::to('/login');
		}

		$flags = array();
		if (User::has(User::CAN_CREATE_NEWS)) {
			$flags[] = 'News verfassen';
		}
		if (User::has(User::CAN_BETA_TEST)) {
			$flags[] = 'Beta-Tester';
		}
		if (User::has(User::CAN_PLAY_MULTIS)) {
			$flags[] = 'Mehrere Parteien';
		}
		$parties    = Auth::user() ? Party::allFor(Auth::user()) : array();
		$newParties = Auth::user() ? NewParty::allFor(Auth::user()) : array();
		return View::make('login', array('flags' => $flags, 'games' => Game::allById(), 'parties' => $parties, 'newParties' => $newParties, 'saved' => $saved));
	}

	public function register() {
		if (!User::canRegister()) {
			return View::make('no-register');
		}

		if (Request::isMethod('POST')) {
			$rules =  array(
				'user'    => 'required|min:3|max:50|unique:users,name',
				'email'   => 'required|email',
				'captcha' => array('required', 'captcha')
			);
			$validator = Validator::make(Input::all(), $rules);
			if ($validator->passes()) {
				$user              = new User();
				$user->name        = Input::get('user');
				$user->email       = Input::get('email');
				$password          = uniqid();
				$user->password    = Hash::make($password);
				$user->passwordmd5 = md5($password);
				$user->save();
				Mail::send('reset-mail', array('user' => $user->name, 'password' => $password), function ($message) use ($user) {
					$message->from('admin@fantasya-pbem.de', 'Fantasya-Administrator');
					$message->to($user->email);
					$message->subject('Fantasya-Registrierung');
				});
				return View::make('registered');
			}
			return View::make('register')->withErrors($validator);
		}
		return View::make('register');
	}

	public function reset() {
		$success = null;
		if (Request::isMethod('POST')) {
			$success = false;
			$user    = User::where('name', '=', Input::get('user'))->where('email', '=', Input::get('email'))->first();
			if ($user) {
				$password          = uniqid();
				$user->password    = Hash::make($password);
				$user->passwordmd5 = md5($password);
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
				foreach (Party::allFor(Auth::user()) as $parties) {
					foreach ($parties as $party) {
						$party->email = $email;
						$party->save();
					}
				}
				foreach (NewParty::allFor(Auth::user()) as $parties) {
				    foreach ($parties as $party) {
				        $party->email = $email;
				        $party->save();
				    }
				}
				$user->email = $email;
				$user->save();
			}
			$password = Input::get('password');
			if ($password) {
				foreach( Party::allFor(Auth::user()) as $parties ) {
					foreach( $parties as $party ) {
						$party->password = md5($password);
						$party->save();
					}
				}
				$user->password    = Hash::make($password);
				$user->passwordmd5 = md5($password);
				$user->save();
			}
		}
		return Redirect::to('/login/saved');
	}

	public function change($what) {
		$game = Game::next();
		Session::put('game', $game->id);
		return Redirect::to(User::countParties($game) > 0 ? 'orders': 'login');
	}

	public function enter() {
		if (User::countAllParties(Game::current()) > 0 && !User::has(User::CAN_PLAY_MULTIS)) {
			return Redirect::to('login');
		}
		$races = array('Aquaner', 'Elf', 'Halbling', 'Mensch', 'Ork', 'Troll', 'Zwerg');
		if (Request::isMethod('POST')) {
			$rules = array(
				'party'       => 'required|min:1|max:50',
				'description' => 'max:500',
				'race'        => 'required|in:' . implode(',', $races),
				'wood'        => 'required|numeric|min:0|max:90',
				'stone'       => 'required|numeric|min:0|max:90',
				'iron'        => 'required|numeric|min:0|max:90',
			);
			$validator = Validator::make(Input::all(), $rules);
			if ($validator->passes()) {
				$wood  = (int)Input::get('wood');
				$stone = (int)Input::get('stone');
				$iron  = (int)Input::get('iron');
				if (($wood + $stone + $iron) <= 90) {
					$party = new NewParty();
					$party->name        = Input::get('party');
					$party->description = Input::get('description');
					$party->email       = Auth::user()->email;
					$party->user_id     = Auth::user()->id;
					$party->rasse       = Input::get('race');
					$party->tarnung     = '';
					$party->holz        = $wood;
					$party->steine      = $stone;
					$party->eisen       = $iron;
					$party->insel       = 0;
					$party->password    = Auth::user()->passwordmd5;
					$party->setConnection(Game::current()->database)->save();
					return Redirect::to('/login');
				}
			}
			return View::make('enter', array('races' => array_combine($races, $races)))->withErrors($validator);
		}
		return View::make('enter', array('races' => array_combine($races, $races)));
	}

	public function revoke($world, $party) {
		$game = Game::find($world);
		if ($game) {
			DB::connection($game->database)->table(NewParty::TABLE)->where('user_id', Auth::user()->id)->where('name', urldecode($party))->delete();
		}
		return Redirect::to('/login');
	}

	public function orders($party = null) {
		$game    = Game::current();
		$parties = Party::allFor(Auth::user());
		$parties = $parties[$game->id];
		if (count($parties) <= 0) {
			return Redirect::to('/login');
		}

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
										  'orders' => $order->getOrders(),
										  'check'  => $order->fcheck()));
	}

	public function report() {
		$game    = Game::current();
		$parties = Party::allFor(Auth::user());
		$parties = $parties[$game->id];
		if (count($parties) <= 0) {
			return Redirect::to('/login');
		}

		$turn = Settings::on($game->database)->find('game.runde')->Value;
		if (Request::isMethod('POST')) {
			$party = Input::get('party');
			$t     = Input::get('turn');;
			if ($party && $t && isset($parties[$party])) {
				$party    = $party ? $parties[$party] : current($parties);
				$turn     = $t;
				$download = new Report($game, $party, $turn);
				if ($download->isValid()) {
					return Response::download($download->getPath());
				}
			}
		}
		$turns = array();
		$p     = array();
		foreach ($parties as $id => $pty) {
			$p[$id]     = $pty->name;
			$turns[$id] = Report::getTurns($game, $pty);
		}
		return View::make('report', array('turn'    => $turn, 'turns' => $turns,
		                                  'parties' => $p));
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
		$myths = DB::table(Myth::TABLE)->orderBy('id', 'DESC')->get();
		return View::make('myths', array('myths' => $myths));
	}

	public function myth() {
		if (!User::canCreateMyths()) {
			return Redirect::to('myths');
		}

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
		$game       = Game::find($id);
		$turn       = Settings::on($game->database)->find('game.runde');
		$lastZAT    = DB::connection($game->database)->table('meldungen')->max('zeit');
		$count      = DB::connection($game->database)->table('partei')->count();
		$parties    = DB::connection($game->database)->table('partei')->select(DB::raw('rasse, COUNT(*) AS count'))->groupBy('rasse')->get();
		$names      = DB::connection($game->database)->table('partei')->select('name', 'beschreibung')->orderBy('name')->get();
		$regions    = DB::connection($game->database)->table('regionen')->select(DB::raw('typ, COUNT(*) AS count'))->where('welt', 1)->groupBy('typ')->orderBy('typ')->get();
		$layers     = DB::connection($game->database)->table('regionen')->select(DB::raw('COUNT(*) AS count'))->groupBy('welt')->orderBy('welt')->get();
		$underworld = DB::connection($game->database)->table('regionen')->select(DB::raw('typ, COUNT(*) AS count'))->where('welt', -1)->groupBy('typ')->orderBy('typ')->get();
		$units      = DB::connection($game->database)->table('einheiten')->select(DB::raw('rasse, COUNT(*) AS units, SUM(person) AS persons'))->whereNotIn('partei', [620480, 1376883])->groupBy('rasse')->orderBy('rasse')->get();
		$monsters   = DB::connection($game->database)->table('einheiten')->select(DB::raw('rasse, COUNT(*) AS units, SUM(person) AS persons'))->whereIn('partei', [620480, 1376883])->groupBy('rasse')->orderBy('rasse')->get();
		$total      = DB::connection($game->database)->table('einheiten')->select(DB::raw('COUNT(*) AS units, SUM(person) AS persons'))->get();
		return View::make('world', array('game' => $game, 'turn' => $turn->Value, 'lastZAT' => $lastZAT, 'count' => $count, 'parties' => $parties, 'names' => $names, 'regions' => $regions, 'underworld' => $underworld, 'layers' => $layers, 'units' => $units, 'monsters' => $monsters, 'total' => $total));
	}

	public function privacy() {
		$cookieExists = isset($_COOKIE['accept_dsgvo']);
		if (Request::isMethod('POST')) {
			if (Input::get('accept')) {
				setcookie('accept_dsgvo', 1, time() + 365 * 24 * 60 * 60, '/');
				$cookieExists = true;
			}
		}
		return View::make('privacy', array('showForm' => !$cookieExists));
	}

}
