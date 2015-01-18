<?php

class FantasyaController extends BaseController {

    public function myths() {
        //$myths = Myth::all();
        $myths = DB::table(Myth::TABLE)->orderBy('id', 'DESC')->get();
        return View::make('myths', array('myths' => $myths));
    }
    
}

