<?php

namespace App\FlightProviders;

interface SearchInterface
{
    /**
     * get available flights
     *
     * @param array $searchParams
     * @return array
     */
    public function search(array $searchParams) : array;

}
