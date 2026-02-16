<?php
namespace app\models;

use Flight;

class BesoinModel{
    private $db;
    private $table='Besoin';

    private $ville_id;
    private $type_id;
    private $quantite_demandee;
    private $quantite_satisfaite;
    private $date_demande;
    private $statut;

    public function __construct($db,$ville_id,$type_id,$quantite_demandee,$quantite_satisfaite,$date_demande,$statut){
        $this->db=Flight::db();
        $this->ville_id=$ville_id;
        $this->type_id=$type_id;
        $this->quantite_demandee=$quantite_demandee;
        $this->quantite_satisfaite=0;
        $this->date_demande=$date_demande;
        $this->statut=$statut;
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


    public function Insert(){
        $query="INSERT INTO {$this->table} (ville_id, type_id, quantite_demandee, quantite_satisfaite, date_demande, statut) VALUES (:ville_id, :type_id, :quantite_demandee, :quantite_satisfaite, :date_demande, :statut) ";
        $stmt=$this->db->prepare($query);
        $stmt->bindParam(':ville_id', $this->ville_id);
        $stmt->bindParam(':type_id', $this->type_id);
        $stmt->bindParam(':quantite_demandee', $this->quantite_demandee);
        $stmt->bindParam(':quantite_satisfaite', $this->quantite_satisfaite);
        $stmt->bindParam(':date_demande', $this->date_demande);
        $stmt->bindParam(':statut', $this->statut);
        return $stmt->execute();
    }


}