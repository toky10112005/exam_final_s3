<?php


use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

use app\controllers\VilleController;
use app\controllers\TypeBesoinController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\AffectationController;
use app\controllers\AchatController;
 
/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->map('/', function() use ($app) {
		$listVille=new VilleController($app);
		$listville=$listVille->list();

		$listTypeBesoin=new TypeBesoinController($app);
		$listtypeBesoin=$listTypeBesoin->list();
		
		$app->render('Saisis', [ 'listVille' => $listville , 'listTypeBesoin' => $listtypeBesoin ]);
	});

	$router->get('/Donne',function() use ($app){
		$besoin=new BesoinController($app);
		$besoin->Insert($_GET['Ville'],$_GET['TypeBesoin'],$_GET['Besoin']);

		$don=new DonController($app);
		$don->Insert($_GET['TypeBesoin'],$_GET['QuantiteDonnee'],$_GET['Donateur']);

		$listVille=new VilleController($app);
		$listville=$listVille->list();

		$listTypeBesoin=new TypeBesoinController($app);
		$listtypeBesoin=$listTypeBesoin->list();
		
		$app->render('Saisis', [ 'listVille' => $listville , 'listTypeBesoin' => $listtypeBesoin, 'message' => 'Données insérées avec succès!' ]);
	});

	$router->get('/dashboard', function() use ($app) {
		$besoinController = new BesoinController($app);
		$dashboard = $besoinController->getDashboard();
		
		$app->render('Dashboard', ['dashboard' => $dashboard]);
	});

	$router->get('/dispatch', function() use ($app) {
		$affectationController = new AffectationController($app);
		$result = $affectationController->simulerDispatch();
		
		$besoinController = new BesoinController($app);
		$dashboard = $besoinController->getDashboard();
		
		$app->render('Dashboard', ['dashboard' => $dashboard, 'message' => $result]);
	});

	// Routes pour les achats
	$router->get('/achats', function() use ($app) {
		$achatController = new AchatController($app);
		$villeController = new VilleController($app);
		
		$ville_id = isset($_GET['ville_id']) ? intval($_GET['ville_id']) : null;
		
		$achats = $achatController->listAchats($ville_id);
		$besoinsRestants = $achatController->getBesoinsRestantsPourAchat();
		$soldeArgent = $achatController->getSoldeArgent();
		$fraisAchat = $achatController->getFraisAchat();
		$villes = $villeController->list();
		
		$app->render('Achats', [
			'achats' => $achats,
			'besoinsRestants' => $besoinsRestants,
			'soldeArgent' => $soldeArgent,
			'fraisAchat' => $fraisAchat,
			'villes' => $villes,
			'ville_id_filtre' => $ville_id
		]);
	});

	$router->get('/effectuer-achat', function() use ($app) {
		$achatController = new AchatController($app);
		$villeController = new VilleController($app);
		
		$besoin_id = isset($_GET['besoin_id']) ? intval($_GET['besoin_id']) : 0;
		$quantite = isset($_GET['quantite']) ? intval($_GET['quantite']) : 0;
		
		$result = $achatController->effectuerAchat($besoin_id, $quantite);
		
		$achats = $achatController->listAchats();
		$besoinsRestants = $achatController->getBesoinsRestantsPourAchat();
		$soldeArgent = $achatController->getSoldeArgent();
		$fraisAchat = $achatController->getFraisAchat();
		$villes = $villeController->list();
		
		$app->render('Achats', [
			'achats' => $achats,
			'besoinsRestants' => $besoinsRestants,
			'soldeArgent' => $soldeArgent,
			'fraisAchat' => $fraisAchat,
			'villes' => $villes,
			'message' => $result['message'],
			'success' => $result['success']
		]);
	});

	$router->get('/config-frais', function() use ($app) {
		$achatController = new AchatController($app);
		$villeController = new VilleController($app);
		
		$nouveau_frais = isset($_GET['frais']) ? floatval($_GET['frais']) : null;
		
		if ($nouveau_frais !== null) {
			$achatController->setFraisAchat($nouveau_frais);
		}
		
		$achats = $achatController->listAchats();
		$besoinsRestants = $achatController->getBesoinsRestantsPourAchat();
		$soldeArgent = $achatController->getSoldeArgent();
		$fraisAchat = $achatController->getFraisAchat();
		$villes = $villeController->list();
		
		$app->render('Achats', [
			'achats' => $achats,
			'besoinsRestants' => $besoinsRestants,
			'soldeArgent' => $soldeArgent,
			'fraisAchat' => $fraisAchat,
			'villes' => $villes,
			'message' => $nouveau_frais !== null ? "Frais d'achat mis à jour à {$fraisAchat}%" : null,
			'success' => true
		]);
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