<?php

class NewParty extends Eloquent {

    const TABLE = 'neuespieler';

    public $timestamps = false;

    protected $table = self::TABLE;

}
