<?php
# import necessary packages from composer
require '../vendor/autoload.php';

# instantiate appplication object
$app = new \Slim\App;

# import REST APIs
require 'core/core.php';

# start application
$app->run();
