<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class FlightSearchTransformer extends TransformerAbstract
{

    public function transform(array $flight) : array
    {
        return [
            'itineraries' => $flight['itineraries'],
            'fare' => $flight['fare'],
        ];
    }
}
