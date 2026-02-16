<?php

use app\controllers\UserController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\CategoreController;
use app\controllers\ProductController;
use app\controllers\PropositionEchangeController;
 
/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->map('/', function() use ($app) {
		$app->render('login');
	});

	$router->get('/LoginForm', function() use ($app){
		$username=$_GET['username']??'';
		$email=$_GET['email']??'';
		$password=$_GET['password']??'';

		$userController=new UserController($app);

		$result=$userController->CheckUser($username,$email,$password);

		if(isset($result['isAdmin']) && $result['isAdmin']==true){

			$listeCategorie=new CategoreController($app);
			$listeCategorie=$listeCategorie->ListCategorie();
			$app->render('categorie',['username'=>$result['donnees']['username'],'id'=>$result['donnees']['id'],'categories'=>$listeCategorie],);

			return;
		}else{
			if(isset($result['error'])){
				$app->render('login',['error'=>$result['error']]);
			}else{
				$productList=new ProductController($app);
				$ProductList=$productList->getProductUser($result['id']);
				$echanger=false;
				$app->render('objet',['username'=>$result['username'],'id'=>$result['id'],'listProduct'=>$ProductList,'echanger'=>$echanger]);
			}
		}
	});

	$router->get('/ChoixCategorie', function() use ($app){
	$categorieID=$_GET['categories'] ?? 1;
	if($categorieID==''){
		//in raha ohatra ka tsisy eto

	}else{
		// $listProduct=new ProductController($app);
		// $listProducts=$listProduct->getProduct($categorieID);

		 $listProduct=new ProductController($app);
		 $listProducts=$listProduct->getProductWithNameProp($categorieID);

		$listeCategorie=new CategoreController($app);
		$listeCategorie=$listeCategorie->ListCategorie();


		$app->render('categorie',['username'=>$_GET['username'],'id'=>$_GET['id'],'categories'=>$listeCategorie,'products'=>$listProducts]);
		
		}
	});

	$router->get('/Liste',function() use ($app){

		$IDcategorie=$_GET['categorie'] ?? 1;
		$ID_user=$_GET['id'] ?? 1;

		if($IDcategorie!=0){
		 	$listProduct=new ProductController($app);
		 	$listProducts=$listProduct->getProductAutre($IDcategorie,$ID_user);

			$listeCategorie=new CategoreController($app);
			$listeCategorie=$listeCategorie->ListCategorie();

			$app->render('ListObjet',['username'=>$_GET['username'],'id'=>$_GET['id'],'categories'=>$listeCategorie,'products'=>$listProducts]);
		}
		else{
			
			//raha 0 le ID d eto no apoitra ilay fionctionalité

		}
		
	} );

	$router->get('/Echanger',function() use ($app){
		$product_id=$_GET['product_id'] ?? 0;
		$id_user=$_GET['id_user'] ?? 0;
		$username=$_GET['username'] ?? '';
		$nomPropriétaire=$_GET['nomPropriétaire'] ?? '';

	$user=new UserController($app);
	$id_propriétaire=$user->getUser('users');

		$id_Propriétaire;

		if($product_id!=0){
				$productList=new ProductController($app);
				$ProductList=$productList->getProductUser($id_user);

				$echanger=true;

				$app->render('objet',['username'=>$username,'id'=>$id_user,'listProduct'=>$ProductList,'echanger'=>$echanger,'idProduit'=>$product_id,'nomPropriétaire'=>$nomPropriétaire]);	
				
		}else{
			//raha tsy misy eto ilay fionctionalité
		}
	});

	$router->get('/EchangeProposition',function() use ($app){
		
		$idProductuser=$_GET['idProductuser'];
		$idProductEchange=$_GET['idProductEchange'];
		$iduser=$_GET['id'];
		$idPropriétaire;


	});

	

	// $router->get('/hello-world/@name', function($name) {
	// 	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	// });

	// $router->group('/api', function() use ($router) {
	// 	$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
	// 	$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
	// 	$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	// });


	
}, [ SecurityHeadersMiddleware::class ]);