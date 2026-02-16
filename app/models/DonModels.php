<?php
namespace app\models;

use Flight;
use PDO;

class DonModels {
    
    private $db;
    private $table = 'Don';
    
    public $type_id;
    public $quantite_donnee;
    public $date_don;
    public $donateur;

    public function __construct($db) {
        $this->db = Flight::db();
    }

    public function Insert(){
      $query="INSERT INTO {$this->table} (type_id, quantite_donnee, date_don, donateur) VALUES (:type_id, :quantite_donnee, NOW(), :donateur) ";
      $stmt=$this->db->prepare($query);
      $params=[
            ':type_id'=>$this->type_id,
            ':quantite_donnee'=>$this->quantite_donnee,
            ':donateur'=>$this->donateur
        ];
      $stmt->execute($params);
    }

    public function list(){
        $query="SELECT d.*, t.nom as type_nom, t.prix_unitaire 
                FROM {$this->table} d 
                JOIN Type_besoin t ON d.type_id = t.id 
                ORDER BY d.date_don ASC";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsNonAffectes(){
        $query="SELECT d.*, t.nom as type_nom, t.prix_unitaire,
                (d.quantite_donnee - COALESCE((SELECT SUM(a.quantite_affectee) FROM Affectation a WHERE a.don_id = d.id), 0)) as quantite_disponible
                FROM {$this->table} d 
                JOIN Type_besoin t ON d.type_id = t.id 
                HAVING quantite_disponible > 0
                ORDER BY d.date_don ASC";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}