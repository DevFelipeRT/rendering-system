<?php

// Include the Autoloader class definition
require_once __DIR__ . '/Autoloader.php';

// Instantiate the autoloader
$loader = new Autoloader();

// Register the base directories for your namespace prefixes
$loader->addNamespace('Config', __DIR__ . '/config');
$loader->addNamespace('Rendering', __DIR__ . '/Rendering');
// You can add your main App namespace as well
$loader->addNamespace('App', __DIR__ . '/App');

// Register the autoloader instance with PHP
$loader->register();