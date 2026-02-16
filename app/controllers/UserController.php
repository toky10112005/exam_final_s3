<?php
namespace app\controllers;

use flight\Engine;
use Flight;
use app\models\UserModel;

class UserController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

  public function CheckUser($username, $email, $password) {
        $db = $this->app->get('db');
        $userModel = new UserModel($db, $username, $email, $password);
        
        if (!$userModel->Validate()) {
            return ['error' => implode(', ', $userModel->getErrors())];
        }

        $admin=$userModel->isAdmin();

        if($admin){
            return $admin;
        }

        $resultUser=$userModel->CheckUser();

       if($resultUser){
            return $resultUser;
       }else{
            return $userModel->getErrors();
       }
        
    }

    function getUser(){
        
    }
}