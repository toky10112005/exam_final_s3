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

    /**
     * Dispatch proportionnel par ordre de grandeur croissante.
     * Pour chaque don, les besoins du même type sont triés par quantité demandée croissante.
     * Chaque besoin reçoit une part proportionnelle : (demande_i / somme_demandes) * quantité_don.
     */
    public function simulerDispatchProportionnel() {
        $db = Flight::db();
        
        $donModel = new DonModels($db);
        $dons = $donModel->list();
        
        $besoinModel = new BesoinModels($db);
        $besoins = $besoinModel->getBesoinsNonSatisfaits();
        
        $affectationModel = new AffectationModels($db);
        $totalAffectations = 0;
        
        foreach ($dons as $don) {
            // Quantité disponible pour ce don
            $totalDejaAffecte = $affectationModel->getTotalAffecteParDon($don['id']);
            $quantiteDisponible = $don['quantite_donnee'] - $totalDejaAffecte;
            
            if ($quantiteDisponible <= 0) {
                continue;
            }
            
            // Filtrer les besoins du même type avec un reste > 0
            $besoinsMemeType = [];
            foreach ($besoins as &$besoin) {
                if ($besoin['type_id'] == $don['type_id']) {
                    $reste = $besoin['quantite_demandee'] - $besoin['quantite_satisfaite'];
                    if ($reste > 0) {
                        $besoinsMemeType[] = &$besoin;
                    }
                }
            }
            unset($besoin);
            
            if (empty($besoinsMemeType)) {
                continue;
            }
            
            // Trier par quantité demandée croissante (ordre de grandeur croissante)
            usort($besoinsMemeType, function($a, $b) {
                return ($a['quantite_demandee'] - $a['quantite_satisfaite']) - ($b['quantite_demandee'] - $b['quantite_satisfaite']);
            });
            
            // Calculer la somme totale des restes pour ce type
            $sommeRestes = 0;
            foreach ($besoinsMemeType as $b) {
                $sommeRestes += ($b['quantite_demandee'] - $b['quantite_satisfaite']);
            }
            
            if ($sommeRestes <= 0) {
                continue;
            }
            
            // Distribuer proportionnellement, en parcourant par ordre croissant
            $resteDisponible = $quantiteDisponible;
            foreach ($besoinsMemeType as &$b) {
                $restesBesoin = $b['quantite_demandee'] - $b['quantite_satisfaite'];
                
                // Part proportionnelle = (reste_besoin / somme_restes) * quantite_don
                $partProportionnelle = ($restesBesoin / $sommeRestes) * $quantiteDisponible;
                
                // On arrondit à l'entier inférieur pour ne pas dépasser
                $partProportionnelle = floor($partProportionnelle);
                
                // Ne pas affecter plus que le reste du besoin ni plus que ce qui est disponible
                $quantiteAAffecter = min($partProportionnelle, $restesBesoin, $resteDisponible);
                
                if ($quantiteAAffecter > 0) {
                    $affectationModel->insert($don['id'], $b['id'], $quantiteAAffecter);
                    $besoinModel->updateQuantiteSatisfaite($b['id'], $quantiteAAffecter);
                    
                    $b['quantite_satisfaite'] += $quantiteAAffecter;
                    $resteDisponible -= $quantiteAAffecter;
                    $totalAffectations++;
                }
                
                if ($resteDisponible <= 0) {
                    break;
                }
            }
            unset($b);
            
            // S'il reste des unités (dues aux arrondis), on les distribue au premier besoin non satisfait (le plus petit)
            if ($resteDisponible > 0) {
                foreach ($besoinsMemeType as &$b) {
                    $restesBesoin = $b['quantite_demandee'] - $b['quantite_satisfaite'];
                    if ($restesBesoin > 0 && $resteDisponible > 0) {
                        $quantiteAAffecter = min($restesBesoin, $resteDisponible);
                        $affectationModel->insert($don['id'], $b['id'], $quantiteAAffecter);
                        $besoinModel->updateQuantiteSatisfaite($b['id'], $quantiteAAffecter);
                        
                        $b['quantite_satisfaite'] += $quantiteAAffecter;
                        $resteDisponible -= $quantiteAAffecter;
                        $totalAffectations++;
                    }
                    if ($resteDisponible <= 0) {
                        break;
                    }
                }
                unset($b);
            }
        }
        
        return "Dispatch proportionnel effectué: $totalAffectations affectations réalisées.";
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

    /**
     * Réinitialise toutes les données modifiées par les dispatches/achats
     * - Supprime toutes les affectations
     * - Supprime tous les achats
     * - Remet quantite_satisfaite à 0 et statut à 'EN_ATTENTE' pour tous les besoins
     */
    public function resetData() {
        $db = Flight::db();
        
        try {
            $db->beginTransaction();
            
            // 1. Supprimer toutes les affectations
            $db->exec("DELETE FROM bnjrc_Affectation");
            
            // 2. Supprimer tous les achats
            $db->exec("DELETE FROM bnjrc_Achat");
            
            // 3. Réinitialiser tous les besoins
            $db->exec("UPDATE bnjrc_Besoin SET quantite_satisfaite = 0, statut = 'EN_ATTENTE'");
            
            // 4. Remettre les frais d'achat par défaut
            $db->exec("UPDATE bnjrc_Config SET valeur = '10' WHERE cle = 'frais_achat_pourcent'");
            
            $db->commit();
            return "Données réinitialisées avec succès.";
        } catch (\Exception $e) {
            $db->rollBack();
            return "Erreur lors de la réinitialisation: " . $e->getMessage();
        }
    }
}
