<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/docs', function (Request $request, Response $response) {
        $html = file_get_contents(__DIR__ . '/../../public/swagger.html');
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/openapi.yaml', function (Request $request, Response $response) {
        $yaml = file_get_contents(__DIR__ . '/../../docs/openapi.yaml');
        $response->getBody()->write($yaml);
        return $response->withHeader('Content-Type', 'application/yaml');
    });
};