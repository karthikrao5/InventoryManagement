<?php


// REST API routes
$app->group('/v1', function() {
	$this->group('/inventory', function() {
		$this->get('', 'ApiController:getAll');
	});
});





// Front-end routes


// test routes (ignore these)
$app->get('/', 'HomeController:index');

$app->get('/home', function($request, $response) {
	// $this->logger->info("reached /home");
	return $this->view->render($response, 'home.twig');
});