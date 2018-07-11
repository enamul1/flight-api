<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

trait ResponseTrait
{
    /**
     * Status code of response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Fractal manager instance
     *
     * @var Manager
     */
    protected $fractal;

    /**
     * Set fractal Manager instance
     *
     * @param Manager $fractal
     */
    public function setFractal(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return ResponseTrait
     */
    public function setStatusCode(int $statusCode) : self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Send custom data response
     *
     * @param int $status
     * @param string $message
     * @return JsonResponse
     */
    public function sendCustomResponse(int $status, string $message) : JsonResponse
    {
        return response()->json(['status' => $status, 'message' => $message], $status);
    }

    /**
     * Send this response when api user provide fields that doesn't exist in our application
     *
     * @param string $errors
     * @return JsonResponse
     */
    public function sendUnknownFieldResponse(string $errors) : JsonResponse
    {
        return response()->json((['status' => 400, 'unknown_fields' => $errors]), 400);
    }

    /**
     * Send this response when api user provide filter that doesn't exist in our application
     *
     * @param string $errors
     * @return JsonResponse
     */
    public function sendInvalidFilterResponse(string $errors) : JsonResponse
    {
        return response()->json((['status' => 400, 'invalid_filters' => $errors]), 400);
    }

    /**
     * Send this response when api user provide incorrect data type for the field
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function sendInvalidFieldResponse(array $errors) : JsonResponse
    {
        return response()->json((['status' => 400, 'invalid_fields' => $errors]), 400);
    }

    /**
     * Send this response when a api user try access a resource that they don't belong
     *
     * @return JsonResponse
     */
    public function sendForbiddenResponse() : JsonResponse
    {
        return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
    }

    /**
     * Send 404 not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public function sendNotFoundResponse(string $message = '') : JsonResponse
    {
        if ($message === '') {
            $message = 'The requested resource was not found';
        }

        return response()->json(['status' => 404, 'message' => $message], 404);
    }

    /**
     * Send empty data response
     *
     * @return JsonResponse
     */
    public function sendEmptyDataResponse() : JsonResponse
    {
        return response()->json(['data' => new \StdClass()]);
    }

    /**
     * Return collection response from the application
     *
     * @param array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection $collection
     * @param \Closure|TransformerAbstract $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback) : JsonResponse
    {
        $resource = new Collection($collection, $callback);

        //set empty data pagination
        if (empty($collection)) {
            $collection = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $resource = new Collection($collection, $callback);
        }
        $resource->setPaginator(new IlluminatePaginatorAdapter($collection));

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Return single item response from the application
     *
     * @param Model $item
     * @param \Closure|TransformerAbstract $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItem($item, $callback) : JsonResponse
    {
        $resource = new Item($item, $callback);
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Return a json response from the application
     *
     * @param array $array
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = []) : JsonResponse
    {
        return response()->json($array, $this->statusCode, $headers);
    }
}
