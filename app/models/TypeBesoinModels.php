<?php
namespace app\models;

use Flight;

class TypeBesoinModels{
    private $db;
    private $table='Type_besoin';

    private $nom;
    private $categorie;
    private $prix_unitaire;

    public function __construct($db,$nom,$categorie,$prix_unitaire){
        $this->db=Flight::db();
        $this->nom=$nom;
        $this->categorie=$categorie;
        $this->prix_unitaire=$prix_unitaire;
    }


    public function list(){
        $sql="SELECT * FROM $this->table";
        $stmt=$this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}