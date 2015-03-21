<?php

class Party extends Eloquent {

    const TABLE = 'partei';

    protected $table = self::TABLE;

    public static function allFor(User $user) {
        $parties = array();
        foreach (Game::all() as $game) {
            $p = array();
            foreach (Party::on($game->database)->where('email', '=', $user->email)->get() as $party) {
                $p[$party->id] = $party;
            }
            $parties[$game->id] = $p;
        }
        return $parties;
    }

}
