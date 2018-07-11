<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use App\Transformers\AirportsTransformer;
use Illuminate\Support\Facades\Input;
use App\FlightProviders\FlightProviderFactory;

class AirportsController extends Controller
{

    /**
     * Instance of AirportsTransformer
     *
     * @var AirportsTransformer
     */
    private $airportsTransformer;

    /**
     * Instance of FlightProviderFactory
     *
     * @var FlightProviderFactory
     */
    private $flightProviderFactory;

    public function __construct(AirportsTransformer $airportsTransformer)
    {
        $this->airportsTransformer = $airportsTransformer;
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
        $value = Input::get('name');
        $airports = Airport::where('airport_name', 'LIKE', '%' . $value . '%')
            ->orWhere('airport_code', 'LIKE', '%' . $value . '%')
            ->limit(30)
            ->get();
        $result = [];
        $i = 0;
        foreach ($airports as $airport) {
            $result[$i]['label'] = $airport->airport_name;
            $result[$i]['value'] = $airport->airport_code;
            $i++;
        }
        $result = $this->createPagination($result, 1, 30);

        return $this->setStatusCode(200)->respondWithCollection($result, $this->airportsTransformer);
    }

    public function autoComplete(Request $request, FlightProviderFactory $flightProviderFactory)
    {
        $this->flightProviderFactory = $flightProviderFactory;
        $airports = $this->flightProviderFactory->get('iatacodes', 'autocomplete')->search($request->all());

        $result = $this->createPagination($airports, 1, 30);
        return $this->setStatusCode(200)->respondWithCollection($result, $this->airportsTransformer);
    }
}
