<?php
namespace App\FlightProviders\Amadeus;

class Autocomplete
{
    /**
     * @inheritdoc
     */
    public function search(array $searchParams) : array
    {
        $term = $searchParams['term'];
        $cacheKey = 'amadeus_flight_search_' .$term;
        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }

        try {
            $client = new \GuzzleHttp\Client();
            $apiUrl = env('AMADEUS_AUTO_COMPLETE_API_URL').'?apikey='
                .env('AMADEUS_API_KEY').'&'.http_build_query($searchParams);
            $res = $client->request('GET', $apiUrl);
            $result['status'] = $res->getStatusCode();
            $result['results'] = json_decode($res->getBody()->getContents(),true);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $result['status'] = 500;
            $result['message'] = $e->getMessage();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // This will catch all 400 level errors.
            return json_decode($e->getResponse()->getBody(),true);
        }
        \Cache::put($cacheKey, $result, 100);
        return $result;
    }
}

