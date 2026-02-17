<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\AffectationModels;
use app\models\DonModels;
use app\models\BesoinModels;

class SimulationController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Simule le dispatch sans enregistrer en base de données
     * Retourne un aperçu des affectations qui seraient faites
     */
    
    public function simuler() {
        $db = Flight::db();
        
        $donModel = new DonModels($db);
        $dons = $donModel->getDonsNonAffectes();
        
        $besoinModel = new BesoinModels($db);
        $besoins = $besoinModel->getBesoinsNonSatisfaits();
        
        $simulation = [];
        $totalQuantiteAffectee = 0;
        $totalValeurAffectee = 0;
        $besoinsSatisfaits = 0;
        
        // Copie des besoins pour simulation
        $besoinsSimules = [];
        foreach ($besoins as $besoin) {
            $besoinsSimules[$besoin['id']] = [
                'id' => $besoin['id'],
                'ville_nom' => $besoin['ville_nom'],
                'type_nom' => $besoin['type_nom'],
                'type_id' => $besoin['type_id'],
                'prix_unitaire' => $besoin['prix_unitaire'],
                'quantite_demandee' => $besoin['quantite_demandee'],
                'quantite_satisfaite' => $besoin['quantite_satisfaite'],
                'quantite_restante' => $besoin['quantite_demandee'] - $besoin['quantite_satisfaite']
            ];
        }
        
        foreach ($dons as $don) {
            $quantiteDisponible = $don['quantite_disponible'];
            
            if ($quantiteDisponible <= 0) {
                continue;
            }
            
            foreach ($besoinsSimules as &$besoin) {
                if ($besoin['type_id'] != $don['type_id']) {
                    continue;
                }
                
                if ($besoin['quantite_restante'] <= 0) {
                    continue;
                }
                
                $quantiteAAffecter = min($quantiteDisponible, $besoin['quantite_restante']);
                
                if ($quantiteAAffecter > 0) {
                    $valeurAffectee = $quantiteAAffecter * $besoin['prix_unitaire'];
                    
                    $simulation[] = [
                        'don_id' => $don['id'],
                        'donateur' => $don['donateur'] ?? 'Anonyme',
                        'besoin_id' => $besoin['id'],
                        'ville_nom' => $besoin['ville_nom'],
                        'type_nom' => $besoin['type_nom'],
                        'quantite_affectee' => $quantiteAAffecter,
                        'prix_unitaire' => $besoin['prix_unitaire'],
                        'valeur_affectee' => $valeurAffectee
                    ];
                    
                    $besoin['quantite_restante'] -= $quantiteAAffecter;
                    $quantiteDisponible -= $quantiteAAffecter;
                    $totalQuantiteAffectee += $quantiteAAffecter;
                    $totalValeurAffectee += $valeurAffectee;
                    
                    if ($besoin['quantite_restante'] <= 0) {
                        $besoinsSatisfaits++;
                    }
                    
                    if ($quantiteDisponible <= 0) {
                        break;
                    }
                }
            }
        }
        
        return [
            'success' => true,
            'simulation' => $simulation,
            'total_affectations' => count($simulation),
            'total_quantite' => $totalQuantiteAffectee,
            'total_valeur' => $totalValeurAffectee,
            'besoins_satisfaits' => $besoinsSatisfaits
        ];
    }

    /**
     * Valide et enregistre vraiment les affectations en base de données
     */
    public function valider() {
        $db = Flight::db();
        
        $donModel = new DonModels($db);
        $dons = $donModel->getDonsNonAffectes();
        
        $besoinModel = new BesoinModels($db);
        $besoins = $besoinModel->getBesoinsNonSatisfaits();
        
        $affectationModel = new AffectationModels($db);
        $totalAffectations = 0;
        $totalValeur = 0;
        
        foreach ($dons as $don) {
            $quantiteDisponible = $don['quantite_disponible'];
            
            if ($quantiteDisponible <= 0) {
                continue;
            }
            
            foreach ($besoins as &$besoin) {
                if ($besoin['type_id'] != $don['type_id']) {
                    continue;
                }
                
                $quantiteRestante = $besoin['quantite_demandee'] - $besoin['quantite_satisfaite'];
                
                if ($quantiteRestante <= 0) {
                    continue;
                }
                
                $quantiteAAffecter = min($quantiteDisponible, $quantiteRestante);
                
                if ($quantiteAAffecter > 0) {
                    // Enregistrer l'affectation en base
                    $affectationModel->insert($don['id'], $besoin['id'], $quantiteAAffecter);
                    
                    // Mettre à jour le besoin
                    $besoinModel->updateQuantiteSatisfaite($besoin['id'], $quantiteAAffecter);
                    
                    $valeur = $quantiteAAffecter * $besoin['prix_unitaire'];
                    $totalValeur += $valeur;
                    
                    $besoin['quantite_satisfaite'] += $quantiteAAffecter;
                    $quantiteDisponible -= $quantiteAAffecter;
                    $totalAffectations++;
                    
                    if ($quantiteDisponible <= 0) {
                        break;
                    }
                }
            }
        }
        
        return [
            'success' => true,
            'message' => "Dispatch validé avec succès!",
            'total_affectations' => $totalAffectations,
            'total_valeur' => $totalValeur
        ];
    }
}
