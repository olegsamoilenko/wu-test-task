<?php

namespace App\Controllers;

use App\Services\PostcodeService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Requests\PostcodeRequest;

/**
 * Controller for handling postcode-related API requests.
 */
class PostcodeController
{
    private PostcodeService $service;

    /**
     * PostcodeController constructor.
     *
     * @param PostcodeService $service
     */
    public function __construct(PostcodeService $service)
    {
        $this->service = $service;
    }

    /**
     * Returns a list of postcodes with optional filters and pagination.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $results = $this->service->getPostcodes($params);

        $response->getBody()->write(json_encode($results, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Adds one or more postcode records after validation.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function add(Request $request, Response $response): Response
    {
        try {
            $items = PostcodeRequest::validateMany($request);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $result = $this->service->addPostcodes($items);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Deletes a postcode record by ID.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $code = $args['code'] ?? null;

        if (!$code) {
            $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Code not provided']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $result = $this->service->deletePostcode($code);
        $status = $result['status'] === 'deleted' ? 200 : 400;

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}