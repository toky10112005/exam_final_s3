<?php
namespace app\models;

use Flight;
use PDO;

class BesoinModels{
    private $db;
    private $table='Besoin';

    private $ville_id;
    private $type_id;
    private $quantite_demandee;
    private $quantite_satisfaite;
    private $date_demande;
    private $statut;

    public function __construct($db){
        $this->db=Flight::db();
    }

    public function insert($ville_id, $type_id, $quantite_demandee){
        $query="INSERT INTO {$this->table} (ville_id, type_id, quantite_demandee, quantite_satisfaite, date_demande, statut) VALUES (:ville_id, :type_id, :quantite_demandee, 0, NOW(), 'EN_ATTENTE') ";
        $stmt=$this->db->prepare($query);
        $params=[
            ':ville_id'=>$ville_id,
            ':type_id'=>$type_id,
            ':quantite_demandee'=>$quantite_demandee
        ];
        return $stmt->execute($params);
    }

    public function listeVillesAvecBesoins() {
        $sql = "
            SELECT 
                v.id AS ville_id,
                v.nom AS ville_nom,
                r.nom AS region_nom,
                b.type_id,
                t.nom AS type_nom,
                t.categorie,
                t.prix_unitaire,
                SUM(b.quantite_demandee) AS total_demande,
                SUM(b.quantite_satisfaite) AS total_satisfait,
                SUM(b.quantite_demandee - b.quantite_satisfaite) AS total_reste,
                SUM(b.quantite_demandee * t.prix_unitaire) AS valeur_totale_demande,
                SUM(b.quantite_satisfaite * t.prix_unitaire) AS valeur_totale_satisfaite,
                COUNT(b.id) AS nombre_besoins
            FROM Ville v
            JOIN Region r ON v.region_id = r.id
            LEFT JOIN Besoin b ON v.id = b.ville_id
            LEFT JOIN Type_besoin t ON b.type_id = t.id
            GROUP BY v.id, v.nom, r.nom, b.type_id, t.nom, t.categorie, t.prix_unitaire
            ORDER BY r.nom, v.nom, t.categorie
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function list(){
        $query="SELECT b.*, v.nom as ville_nom, t.nom as type_nom, t.prix_unitaire 
                FROM {$this->table} b 
                JOIN Ville v ON b.ville_id = v.id 
                JOIN Type_besoin t ON b.type_id = t.id 
                ORDER BY b.date_demande ASC";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsNonSatisfaits(){
        $query="SELECT b.*, v.nom as ville_nom, t.nom as type_nom, t.prix_unitaire 
                FROM {$this->table} b 
                JOIN Ville v ON b.ville_id = v.id 
                JOIN Type_besoin t ON b.type_id = t.id 
                WHERE b.quantite_demandee > b.quantite_satisfaite
                ORDER BY b.date_demande ASC";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantiteSatisfaite($besoin_id, $quantite){
        $query="UPDATE {$this->table} SET quantite_satisfaite = quantite_satisfaite + :quantite, 
                statut = CASE 
                    WHEN quantite_satisfaite + :quantite2 >= quantite_demandee THEN 'SATISFAIT' 
                    ELSE 'PARTIEL' 
                END 
                WHERE id = :id";
        $stmt=$this->db->prepare($query);
        $params=[
            ':quantite'=>$quantite,
            ':quantite2'=>$quantite,
            ':id'=>$besoin_id
        ];
        return $stmt->execute($params);
    }


}