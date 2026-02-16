<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\VilleModels;

class VilleController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function list() {
        $db=Flight::db();
        $villeModel = new VilleModels($db, '', '');
        $villes = $villeModel->list();
        return $villes;
    }
    
}