<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\BesoinModels;

class BesoinController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function Insert($ville_id, $type_id, $quantite_demandee) {
        $db=Flight::db();
        $besoin=new BesoinModels($db);
        $besoin->insert($ville_id, $type_id, $quantite_demandee);
        return $besoin;
    }

    public function getDashboard() {
        $db=Flight::db();
        $besoin=new BesoinModels($db);
        return $besoin->listeVillesAvecBesoins();
    }

    public function getBesoinsNonSatisfaits() {
        $db=Flight::db();
        $besoin=new BesoinModels($db);
        return $besoin->getBesoinsNonSatisfaits();
    }

    public function updateQuantiteSatisfaite($besoin_id, $quantite) {
        $db=Flight::db();
        $besoin=new BesoinModels($db);
        return $besoin->updateQuantiteSatisfaite($besoin_id, $quantite);
    }
}