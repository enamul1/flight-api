<?php

namespace App\FlightProviders\Iatacodes;

class Autocomplete
{
    /**
     * @inheritdoc
     */
    public function search(array $searchParams): array
    {
        $term = $searchParams['term'];
        $cacheKey = 'iata-codes-airports-autocomplete-' . $term;

        // return from cache if presents
        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }

        $result = [];
        try {
            $client = new \GuzzleHttp\Client();
            $apiUrl = env('IATACODES_AUTO_COMPLETE_API_URL') . '?api_key='
                . env('IATACODES_API_KEY') . '&' . 'query=' . $term;
            $res = $client->request('GET', $apiUrl);
            $result['status'] = $res->getStatusCode();
            $result['results'] = json_decode($res->getBody()->getContents(), true);
            $airports = [];
            if (isset($result['results']['response']['airports_by_cities'])) {
                foreach ($result['results']['response']['airports_by_cities'] as $key => $airport) {
                    $airports[$key]['value'] = $airport['code'];
                    $airports[$key]['label'] = $airport['name'];
                }
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $result['status'] = 500;
            $result['message'] = $e->getMessage();

            return $result;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // This will catch all 400 level errors.
            return json_decode($e->getResponse()->getBody(), true);
        }

        \Cache::put($cacheKey, $airports, 100);

        return $airports;
    }
}

