<?php

// core
require_once __DIR__ . '/app/App.php';
require_once __DIR__ . '/app/Environment.php';

// create the application
$app = new App();

// load all the environment variables
$app->environment->load();