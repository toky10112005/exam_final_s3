<?php
namespace app\models;

use Flight;
use PDO;

class VilleModels{
    private $db;
    private $table='Ville';

    private $nom;
    private $region_id;

    public function __construct($db,$nom,$region_id){
        $this->db=Flight::db();
        $this->nom=$nom;
        $this->region_id=$region_id;
    }

    public function list(){
        $query="SELECT * FROM {$this->table}";
        $stmt=$this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
