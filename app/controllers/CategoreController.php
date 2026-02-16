<?php
namespace app\controllers;

use flight\Engine;
use Flight;
use app\models\CategorieModel;

class CategoreController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function ListCategorie() {

        $db = Flight::db();
        $categorieModel = new CategorieModel($db, '');
        return $categorieModel->ListCategorie();
        
    }
}