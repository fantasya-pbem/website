<?php

class FantasyaController extends BaseController {

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

