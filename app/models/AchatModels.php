<?php
namespace app\models;

use Flight;
use PDO;

class AchatModels {
    
    private $db;
    private $table = 'bnjrc_Achat';
    
    public function __construct($db) {
        $this->db = Flight::db();
    }


    public function getFraisAchatPourcent() {
        $query = "SELECT valeur FROM bnjrc_Config WHERE cle = 'frais_achat_pourcent'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? floatval($result['valeur']) : 10.0; // Défaut 10%
    }

    
    public function setFraisAchatPourcent($pourcent) {
        $query = "UPDATE bnjrc_Config SET valeur = :valeur WHERE cle = 'frais_achat_pourcent'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':valeur' => $pourcent]);
    }

    
    public function getQuantiteDisponibleEnDon($type_id) {
        $query = "SELECT SUM(d.quantite_donnee - COALESCE(
                    (SELECT SUM(a.quantite_affectee) FROM bnjrc_Affectation a WHERE a.don_id = d.id), 0
                  )) as quantite_disponible
                  FROM bnjrc_Don d
                  WHERE d.type_id = :type_id
                  HAVING quantite_disponible > 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':type_id' => $type_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? floatval($result['quantite_disponible']) : 0;
    }

  
    public function getArgentDisponible() {
        $query = "SELECT SUM(d.quantite_donnee - COALESCE(
                    (SELECT SUM(a.quantite_affectee) FROM bnjrc_Affectation a WHERE a.don_id = d.id), 0
                  ) - COALESCE(
                    (SELECT SUM(ac.montant_total) FROM bnjrc_Achat ac 
                     JOIN bnjrc_Besoin b ON ac.besoin_id = b.id 
                     WHERE b.type_id = d.type_id AND d.type_id = (SELECT id FROM bnjrc_Type_besoin WHERE categorie = 'Argent' LIMIT 1)), 0
                  )) as argent_disponible
                  FROM bnjrc_Don d
                  JOIN bnjrc_Type_besoin t ON d.type_id = t.id
                  WHERE t.categorie = 'Argent'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['argent_disponible'] ? floatval($result['argent_disponible']) : 0;
    }

   
    public function getSoldeArgent() {
        // Total des dons en argent
        $queryDons = "SELECT COALESCE(SUM(d.quantite_donnee), 0) as total_dons
                      FROM bnjrc_Don d
                      JOIN bnjrc_Type_besoin t ON d.type_id = t.id
                      WHERE t.categorie = 'Argent'";
        $stmt = $this->db->prepare($queryDons);
        $stmt->execute();
        $totalDons = $stmt->fetch(PDO::FETCH_ASSOC)['total_dons'];

        
        $queryAchats = "SELECT COALESCE(SUM(montant_total), 0) as total_achats FROM {$this->table}";
        $stmt = $this->db->prepare($queryAchats);
        $stmt->execute();
        $totalAchats = $stmt->fetch(PDO::FETCH_ASSOC)['total_achats'];

        return floatval($totalDons) - floatval($totalAchats);
    }

    
    public function effectuerAchat($besoin_id, $quantite, $prix_unitaire, $frais_pourcent) {
        $montant_total = $quantite * $prix_unitaire * (1 + $frais_pourcent / 100);
        
        $query = "INSERT INTO {$this->table} (besoin_id, quantite_achetee, prix_unitaire, frais_pourcent, montant_total) 
                  VALUES (:besoin_id, :quantite, :prix_unitaire, :frais_pourcent, :montant_total)";
        $stmt = $this->db->prepare($query);
        $params = [
            ':besoin_id' => $besoin_id,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prix_unitaire,
            ':frais_pourcent' => $frais_pourcent,
            ':montant_total' => $montant_total
        ];
        return $stmt->execute($params);
    }

    /**
     * Liste tous les achats avec filtrage optionnel par ville
     */
    public function list($ville_id = null) {
        $query = "SELECT ac.*, b.quantite_demandee, b.quantite_satisfaite,
                         v.nom as ville_nom, t.nom as type_nom, t.categorie
                  FROM {$this->table} ac
                  JOIN bnjrc_Besoin b ON ac.besoin_id = b.id
                  JOIN bnjrc_Ville v ON b.ville_id = v.id
                  JOIN bnjrc_Type_besoin t ON b.type_id = t.id";
        
        if ($ville_id !== null && $ville_id > 0) {
            $query .= " WHERE v.id = :ville_id";
        }
        
        $query .= " ORDER BY ac.date_achat DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($ville_id !== null && $ville_id > 0) {
            $stmt->execute([':ville_id' => $ville_id]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les besoins restants (non satisfaits) en Nature et Matériaux pour les achats
     */
    public function getBesoinsRestantsPourAchat() {
        $query = "SELECT b.id, b.ville_id, b.type_id, b.quantite_demandee, b.quantite_satisfaite,
                         (b.quantite_demandee - b.quantite_satisfaite) as reste,
                         v.nom as ville_nom, t.nom as type_nom, t.categorie, t.prix_unitaire
                  FROM bnjrc_Besoin b
                  JOIN bnjrc_Ville v ON b.ville_id = v.id
                  JOIN bnjrc_Type_besoin t ON b.type_id = t.id
                  WHERE b.quantite_demandee > b.quantite_satisfaite
                  AND t.categorie IN ('Nature', 'Materiaux')
                  ORDER BY b.date_demande ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
