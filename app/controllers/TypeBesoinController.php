<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\TypeBesoinModels;

class TypeBesoinController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function list() {
        $db=Flight::db();
        $typeBesoinModel = new TypeBesoinModels($db, '', '', '');
        $typeBesoin = $typeBesoinModel->list();
        return $typeBesoin;
    }
}