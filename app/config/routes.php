<?php


use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

use app\controllers\VilleController;
use app\controllers\TypeBesoinController;
 
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

	


	

	// $router->get('/hello-world/@name', function($name) {
	// 	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	// });

	// $router->group('/api', function() use ($router) {
	// 	$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
	// 	$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
	// 	$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	// });


	
}, [ SecurityHeadersMiddleware::class ]);