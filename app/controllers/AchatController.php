<?php

namespace app\controllers;

use flight\Engine;
use Flight;

use app\models\AchatModels;
use app\models\BesoinModels;

class AchatController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Récupère les frais d'achat configurés
     */
    public function getFraisAchat() {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->getFraisAchatPourcent();
    }

    /**
     * Met à jour les frais d'achat
     */
    public function setFraisAchat($pourcent) {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->setFraisAchatPourcent($pourcent);
    }

    /**
     * Récupère le solde d'argent disponible
     */
    public function getSoldeArgent() {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->getSoldeArgent();
    }

    /**
     * Récupère les besoins restants pour achat
     */
    public function getBesoinsRestantsPourAchat() {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->getBesoinsRestantsPourAchat();
    }

    /**
     * Vérifie si le type existe dans les dons restants
     */
    public function verifierDisponibiliteDon($type_id) {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->getQuantiteDisponibleEnDon($type_id);
    }

    /**
     * Effectue un achat avec validation
     */
    public function effectuerAchat($besoin_id, $quantite) {
        $db = Flight::db();
        $achat = new AchatModels($db);
        $besoinModel = new BesoinModels($db);

        // Récupérer les infos du besoin
        $besoins = $achat->getBesoinsRestantsPourAchat();
        $besoinInfo = null;
        foreach ($besoins as $b) {
            if ($b['id'] == $besoin_id) {
                $besoinInfo = $b;
                break;
            }
        }

        if (!$besoinInfo) {
            return ['success' => false, 'message' => 'Besoin non trouvé ou déjà satisfait.'];
        }

        // Vérifier si ce type existe dans les dons restants
        $quantiteDonDisponible = $achat->getQuantiteDisponibleEnDon($besoinInfo['type_id']);
        if ($quantiteDonDisponible > 0) {
            return [
                'success' => false, 
                'message' => "Erreur: Il reste {$quantiteDonDisponible} unités de '{$besoinInfo['type_nom']}' dans les dons. Utilisez d'abord les dons disponibles avant d'acheter."
            ];
        }

        // Vérifier la quantité demandée
        $reste = $besoinInfo['reste'];
        if ($quantite > $reste) {
            return ['success' => false, 'message' => "La quantité demandée ({$quantite}) dépasse le besoin restant ({$reste})."];
        }

        // Calculer le coût avec frais
        $frais = $achat->getFraisAchatPourcent();
        $coutTotal = $quantite * $besoinInfo['prix_unitaire'] * (1 + $frais / 100);

        // Vérifier le solde disponible
        $solde = $achat->getSoldeArgent();
        if ($coutTotal > $solde) {
            return [
                'success' => false, 
                'message' => "Solde insuffisant. Coût: " . number_format($coutTotal, 2) . " Ar, Solde disponible: " . number_format($solde, 2) . " Ar"
            ];
        }

        // Effectuer l'achat
        $result = $achat->effectuerAchat($besoin_id, $quantite, $besoinInfo['prix_unitaire'], $frais);
        
        if ($result) {
            // Mettre à jour la quantité satisfaite du besoin
            $besoinModel->updateQuantiteSatisfaite($besoin_id, $quantite);
            
            return [
                'success' => true, 
                'message' => "Achat effectué avec succès! {$quantite} x {$besoinInfo['type_nom']} pour " . number_format($coutTotal, 2) . " Ar (frais {$frais}% inclus)"
            ];
        }

        return ['success' => false, 'message' => 'Erreur lors de l\'achat.'];
    }

    /**
     * Liste les achats avec filtrage par ville
     */
    public function listAchats($ville_id = null) {
        $db = Flight::db();
        $achat = new AchatModels($db);
        return $achat->list($ville_id);
    }
}
