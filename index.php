<?php

// core
require_once __DIR__ . '/app/App.php';
require_once __DIR__ . '/app/Controller.php';
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Environment.php';
require_once __DIR__ . '/app/Model.php';
require_once __DIR__ . '/app/Response.php';
require_once __DIR__ . '/app/Router.php';

// models
require_once __DIR__ . '/app/Models/Course.php';
require_once __DIR__ . '/app/Models/Program.php';
require_once __DIR__ . '/app/Models/Semester.php';

// controllers
require_once __DIR__ . '/app/Controllers/HomeController.php';

// create the application
$app = new App();

// load all the environment variables
$app->environment->load();

// register all the routes
$app->router->add('/', ['HomeController', 'first']);
$app->router->add('/second', ['HomeController', 'second']);
$app->router->add('/third', ['HomeController', 'third']);

// intercept incoming request and redirect to proper function
$response = $app->router->handle();

// send response back
http_response_code($response->getStatus());
echo $response->getContent();

// end
exit();
