<?php
namespace app\models;

use Flight;
use PDO;

class AffectationModels{
    private $db;
    private $table='Affectation';

    public function __construct($db){
        $this->db=Flight::db();
    }

    public function insert($don_id, $besoin_id, $quantite_affectee){
        $query="INSERT INTO {$this->table} (don_id, besoin_id, quantite_affectee, date_affectation) VALUES (:don_id, :besoin_id, :quantite_affectee, NOW())";
        $stmt=$this->db->prepare($query);
        $params=[
            ':don_id'=>$don_id,
            ':besoin_id'=>$besoin_id,
            ':quantite_affectee'=>$quantite_affectee
        ];
        return $stmt->execute($params);
    }

    public function list(){
        $query="SELECT a.*, d.donateur, d.quantite_donnee, b.quantite_demandee, v.nom as ville_nom, t.nom as type_nom, t.prix_unitaire
                FROM {$this->table} a 
                JOIN Don d ON a.don_id = d.id 
                JOIN Besoin b ON a.besoin_id = b.id 
                JOIN Ville v ON b.ville_id = v.id 
                JOIN Type_besoin t ON b.type_id = t.id 
                ORDER BY a.date_affectation ASC";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAffectationsParVille(){
        $query="SELECT v.id as ville_id, v.nom as ville_nom, r.nom as region_nom, 
                t.id as type_id, t.nom as type_nom, t.categorie, t.prix_unitaire,
                COALESCE(SUM(a.quantite_affectee), 0) as total_affecte,
                COALESCE(SUM(a.quantite_affectee * t.prix_unitaire), 0) as valeur_affectee
                FROM Ville v
                JOIN Region r ON v.region_id = r.id
                LEFT JOIN Besoin b ON v.id = b.ville_id
                LEFT JOIN Type_besoin t ON b.type_id = t.id
                LEFT JOIN Affectation a ON b.id = a.besoin_id
                GROUP BY v.id, v.nom, r.nom, t.id, t.nom, t.categorie, t.prix_unitaire
                ORDER BY r.nom, v.nom, t.categorie";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAffecteParDon($don_id){
        $query="SELECT COALESCE(SUM(quantite_affectee), 0) as total FROM {$this->table} WHERE don_id = :don_id";
        $stmt=$this->db->prepare($query);
        $stmt->execute([':don_id' => $don_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
