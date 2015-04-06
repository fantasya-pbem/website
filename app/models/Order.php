<?php

class Order  {

    protected $game = '';

    protected $party = 0;

    protected $turn = 0;

    public function __construct(Game $game, Party $party, $turn = null) {
        $this->game  = $game;
        $this->party = $party;
        $this->turn  = $turn > 0 ? (int)$turn : Settings::on($game->database)->find('game.runde')->Value;
    }

    public function getOrders() {
        $file = $this->getPath();
        if (is_file($file)) {
            return file_get_contents($file);
        }
        return '';
    }

    public function setOrders($orders) {
        $file = $this->getPath();
        $dir  = dirname($file);
        umask(0002);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return file_put_contents($file, $orders) > 0;
    }

    public function getPath() {
        $storage = realpath(__DIR__ . '/../storage/orders');
        return $storage . DIRECTORY_SEPARATOR . $this->game->alias . DIRECTORY_SEPARATOR . $this->turn
                        . DIRECTORY_SEPARATOR . $this->party->id . '.order';
    }

}