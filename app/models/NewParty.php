<?php

class NewParty extends Eloquent {

    const TABLE = 'neuespieler';

    public $timestamps = false;

    protected $table = self::TABLE;

    public static function allFor(User $user) {
        $parties = array();
        foreach (Game::all() as $game) {
            $p = array();
            foreach (NewParty::on($game->database)->where('user_id', '=', $user->id)->get() as $party) {
                $p[] = $party;
            }
            $parties[$game->id] = $p;
        }
        return $parties;
    }

}
