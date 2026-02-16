<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\DonModels;

class DonController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function Insert($type_id, $quantite_donnee, $donateur) {
        $db=Flight::db();
        $don=new DonModels($db);
        $don->type_id=$type_id;
        $don->quantite_donnee=$quantite_donnee;
        $don->donateur=$donateur;
        $don->Insert();
        
        return $don;
    }


}
