<?php
namespace app\models;

use Flight;

class CategorieModel{
    private $db;
    private $table='categorie';

    private $categorie;

    public function __construct($db,$categorie){
        $this->db=Flight::db();
        $this->categorie=$categorie;
    }

    public function ListCategorie(){

        $query="SELECT * FROM {$this->table}";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
    }


}