<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\AffectationModels;
use app\models\DonModels;
use app\models\BesoinModels;

class AffectationController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function simulerDispatch() {
        $db = Flight::db();
        
        // Récupérer tous les dons par ordre de date
        $donModel = new DonModels($db);
        $dons = $donModel->list();
        
        // Récupérer tous les besoins non satisfaits par ordre de date
        $besoinModel = new BesoinModels($db);
        $besoins = $besoinModel->getBesoinsNonSatisfaits();
        
        $affectationModel = new AffectationModels($db);
        $totalAffectations = 0;
        
        foreach ($dons as $don) {
            // Calculer la quantité disponible pour ce don
            $totalDejaAffecte = $affectationModel->getTotalAffecteParDon($don['id']);
            $quantiteDisponible = $don['quantite_donnee'] - $totalDejaAffecte;
            
            if ($quantiteDisponible <= 0) {
                continue;
            }
            
            // Parcourir les besoins du même type
            foreach ($besoins as &$besoin) {
                if ($besoin['type_id'] != $don['type_id']) {
                    continue;
                }
                
                $quantiteRestante = $besoin['quantite_demandee'] - $besoin['quantite_satisfaite'];
                
                if ($quantiteRestante <= 0) {
                    continue;
                }
                
                // Calculer la quantité à affecter
                $quantiteAAffecter = min($quantiteDisponible, $quantiteRestante);
                
                if ($quantiteAAffecter > 0) {
                    // Créer l'affectation
                    $affectationModel->insert($don['id'], $besoin['id'], $quantiteAAffecter);
                    
                    // Mettre à jour le besoin
                    $besoinModel->updateQuantiteSatisfaite($besoin['id'], $quantiteAAffecter);
                    
                    // Mettre à jour les compteurs locaux
                    $besoin['quantite_satisfaite'] += $quantiteAAffecter;
                    $quantiteDisponible -= $quantiteAAffecter;
                    $totalAffectations++;
                    
                    if ($quantiteDisponible <= 0) {
                        break;
                    }
                }
            }
        }
        
        return "Dispatch effectué: $totalAffectations affectations réalisées.";
    }

    public function list() {
        $db = Flight::db();
        $affectation = new AffectationModels($db);
        return $affectation->list();
    }

    public function getAffectationsParVille() {
        $db = Flight::db();
        $affectation = new AffectationModels($db);
        return $affectation->getAffectationsParVille();
    }
}
