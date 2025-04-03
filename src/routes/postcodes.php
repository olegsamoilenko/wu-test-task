<?php

use App\Services\PostcodeService;
use Slim\App;
use App\Controllers\PostcodeController;

return function (App $app, \PDO $pdo) {
    $service = new PostcodeService($pdo);
    $controller = new PostcodeController($service);
    $app->get('/api/postcodes', [$controller, 'index']);
    $app->post('/api/postcodes', [$controller, 'add']);
    $app->delete('/api/postcodes/{code}', [$controller, 'delete']);
};
