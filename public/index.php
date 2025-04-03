<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
$pdo = require __DIR__ . '/../src/db.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

(require __DIR__ . '/../src/routes/postcodes.php')($app, $pdo);
(require __DIR__ . '/../src/routes/swagger.php')($app);

$app->run();