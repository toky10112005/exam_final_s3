<?php
namespace app\models;

use Flight;

class UserModel {
    
    private $db;
    private $table = 'users';
    
    public $username;
    public $email;
    public $password;
    private $errors = [];
    
    public function __construct($db, $username, $email, $password) {
        $this->db = Flight::db();
        $this->username = $username;
        $this->email = $email;
        $this->password = $password; 
    }

    public function Insert(){
        $query ="INSERT INTO {$this->table} (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($query);
        $params=[
            ':username' => $this->username,
            ':email' => $this->email,
            ':password' => password_hash($this->password, PASSWORD_DEFAULT) 
        ];
       return $stmt->execute($params);  
    } 

    public function CheckUser(){
        $query="SELECT * FROM {$this->table} WHERE username=:username";
       $stmt=$this->db->prepare($query);
        $params=[
            ':username'=>$this->username
        ];

        $stmt->execute($params);
        $user=$stmt->fetch(\PDO::FETCH_ASSOC);

        if($user){
            if(password_verify($this->password,$user['password'])){
                return ['username'=>$user['username'],'id'=>$user['id']];
            }else{
                return ['error'=>'Invalid password'];
            }
        }else {
            $this->Insert();
            $user=$this->getUser($this->table);
            return $user;
        }
    }

    public function Validate(){
        if(empty($this->username) || strlen($this->username) < 3){
            $this->errors[] = "Username must be at least 3 characters long.";
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            $this->errors[] = "Invalid email format.";
        }

        if(strlen($this->password) < 4){
            $this->errors[] = "Password must be at least 4 characters long.";
        }

        return empty($this->errors);
    }

    public function getErrors(){
        return $this->errors;
    }

    public function getUser($nomTable){
        $query="SELECT * FROM {$nomTable} WHERE username=:username";
         $stmt=$this->db->prepare($query);
          $params=[
                ':username'=>$this->username
          ];
    
          $stmt->execute($params);
          return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $query="SELECT * FROM {$this->table} WHERE id=:id";
         $stmt=$this->db->prepare($query);
          $params=[
                ':id'=>$id
          ];
    
          $stmt->execute($params);
          return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function isAdmin(){
        $nomTable="admin";
       $result=$this->getUser($nomTable);

       if($result){
        if(password_verify($this->password,$result['password'])){

            $valiny=[
                'isAdmin'=>true,
                'donnees'=>$result
            ];

            return $valiny;
        }else{
            return false;
        }
        
       }else{
        return false;
       }



    }
  
}