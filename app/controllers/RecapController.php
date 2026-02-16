<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\BesoinModels;
use app\models\DonModels;
use app\models\AffectationModels;

class RecapController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Récupère les statistiques de récapitulation
     */
    public function getStats() {
        $db = Flight::db();
        $besoinModel = new BesoinModels($db);
        
        // Récupérer tous les besoins
        $besoins = $besoinModel->list();
        
        $besoins_totaux = 0;
        $besoins_satisfaits_count = 0;
        $besoins_partiels_count = 0;
        $besoins_attente_count = 0;
        
        $montant_total_besoins = 0;
        $montant_satisfait = 0;
        
        foreach ($besoins as $besoin) {
            $besoins_totaux++;
            
            $valeur_demandee = $besoin['quantite_demandee'] * $besoin['prix_unitaire'];
            $valeur_satisfaite = $besoin['quantite_satisfaite'] * $besoin['prix_unitaire'];
            
            $montant_total_besoins += $valeur_demandee;
            $montant_satisfait += $valeur_satisfaite;
            
            if ($besoin['quantite_satisfaite'] >= $besoin['quantite_demandee']) {
                $besoins_satisfaits_count++;
            } elseif ($besoin['quantite_satisfaite'] > 0) {
                $besoins_partiels_count++;
            } else {
                $besoins_attente_count++;
            }
        }
        
        $montant_restant = $montant_total_besoins - $montant_satisfait;
        $pourcentage = $montant_total_besoins > 0 
            ? round(($montant_satisfait / $montant_total_besoins) * 100, 2) 
            : 0;
        
        return [
            'besoins_totaux' => $besoins_totaux,
            'besoins_satisfaits' => $besoins_satisfaits_count,
            'besoins_partiels' => $besoins_partiels_count,
            'besoins_attente' => $besoins_attente_count,
            'montant_total_besoins' => $montant_total_besoins,
            'montant_satisfait' => $montant_satisfait,
            'montant_restant' => $montant_restant,
            'pourcentage_completion' => $pourcentage,
            'derniere_maj' => date('d/m/Y H:i:s')
        ];
    }
}
