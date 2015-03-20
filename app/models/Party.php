<?php

class Party extends Eloquent {

    const TABLE = 'partei';

    protected $table = self::TABLE;

    public static function allFor(User $user) {
        $parties = array();
        foreach (Game::all() as $game) {
            $parties[$game->id] = Party::on($game->database)->where('email', '=', $user->email)->get();
        }
        return $parties;
    }

}
