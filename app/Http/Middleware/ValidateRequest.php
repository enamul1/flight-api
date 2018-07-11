<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseTrait;
use Closure;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as Validator;
class ValidateRequest
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $validatorName
     * @return mixed
     */
    public function handle($request, Closure $next, $validatorName)
    {
        $validatorResponse = $this->validateRequest($request, $validatorName);
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        return $next($request);
    }

    /**
     * validate the request against the rules
     *
     * @param Request $request
     * @param $validatorName
     * @return mixed
     */
    private function validateRequest(Request $request, $validatorName)
    {
        $requestMethod = $request->getMethod();

        $rulesRequestType = null;
        switch ($requestMethod) {
            case 'POST':
                $rulesRequestType = 'StoreRequest';
                break;
            case 'PUT':
                $rulesRequestType = 'UpdateRequest';
                break;
            case 'GET':
                $rulesRequestType = 'IndexRequest';
                break;
            default:
                $rulesRequestType = 'StoreRequest';
        }

        $rulesClassName = 'App\Http\Validators\\'.  $validatorName. '\\'. $rulesRequestType;
        $rulesClassInstance = new $rulesClassName();
        $rules = $rulesClassInstance->rules($request);

        $allowableFields = array_merge($rules, ['page' => 'number', 'per_page' => 'number', 'order_by' => 'string', 'order_direction' => 'string|in:asc,desc']);

        $unknownFieldsError = [];
        /**
         * First, check if any unknown fields or filter given
         */
        foreach ($request->all() as $key => $value) {
            //validation for nested input. ex: household.gender
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $subKey = $key. '.' .$subKey;
                    $unknownFieldsError += $this->getUnknownFieldsOrFilters($allowableFields, $subKey, $rulesRequestType);
                }
            } else {
                $unknownFieldsError += $this->getUnknownFieldsOrFilters($allowableFields, $key, $rulesRequestType);
            }
        }

        if (!empty($unknownFieldsError)) {
            return $unknownFieldsError;
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return true;
    }

    /**
     * Check if any unknown fields or filters exists in input
     *
     * @param array $allowableFields
     * @param string $key
     * @param string $rulesRequestType
     * @return array
     */
    private function getUnknownFieldsOrFilters(array $allowableFields, $key, $rulesRequestType) : array
    {
        $unknownFieldsError = [];
        if (!array_key_exists($key, $allowableFields)) {

            // if it is a IndexRequest return invalid filter message
            if (strpos($rulesRequestType, 'IndexRequest') !== false) {
                $unknownFieldsError[$key] = "invalid filter";
            } else {
                $unknownFieldsError[$key] = "field does not exist";
            }
        }

        return $unknownFieldsError;
    }
}
