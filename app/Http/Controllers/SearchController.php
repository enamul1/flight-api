<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FlightProviders\FlightProviderFactory;
use App\Transformers\FlightSearchTransformer;

class SearchController extends Controller
{
    protected $validatorName = 'Search';

    /**
     * Instance of FlightProviderFactory
     *
     * @var FlightProviderFactory
     */
    private $flightProviderFactory;

    /**
     * Instance of FlightSearchTransformer
     *
     * @var FlightSearchTransformer
     */
    private $flightSearchTransformer;

    public function __construct(
        FlightProviderFactory $flightProviderFactory,
        FlightSearchTransformer $flightSearchTransformer)
    {
        $this->flightProviderFactory = $flightProviderFactory;
        $this->flightSearchTransformer = $flightSearchTransformer;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $flights = $this->flightProviderFactory->get('amadeus','search')->search($request->all());
        if ($flights['status'] == 200) {
            $flights = $this->createPagination($flights['data'],$request->input('page'));
            return $this->setStatusCode(200)->respondWithCollection($flights, $this->flightSearchTransformer);
        }
        return $this->sendCustomResponse($flights['status'],$flights['message']);
    }
}
