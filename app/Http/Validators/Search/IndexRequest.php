<?php

namespace App\Http\Validators\Search;

class IndexRequest
{
    public function rules()
    {
        return [
            'origin' => 'required',
            'destination' => 'required',
            'departure_date' => 'required|date|after:' . date('Y-m-d',strtotime("-1 days")),
            'return_date' => 'date|after:' . date('Y-m-d',strtotime("-1 days")),
            'arrive_by' => 'date|after:' . date('Y-m-d',strtotime("-1 days")),
            'return_by' => 'date|after:' . date('Y-m-d',strtotime("-1 days")),
            'adults' => 'required',
            'children' => '',
            'infants' => '',
            'nonstop' => '',
            'max_price' => '',
            'currency' => '',
            'travel_class' => '',
        ];
    }
}
