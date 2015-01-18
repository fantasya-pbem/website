<?php

class Settings extends Eloquent {

    const TABLE = 'settings';

    protected $primaryKey = 'Name';
            
    public $timestamps = false;
   
}

