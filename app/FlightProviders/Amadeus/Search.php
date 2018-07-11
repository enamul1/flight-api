<?php
namespace App\FlightProviders\Amadeus;

use App\FlightProviders\SearchInterface;

class Search implements SearchInterface
{
    /**
     * @inheritdoc
     */
    public function search(array $searchParams) : array
    {
        $origin = $searchParams['origin'];
        $destination = $searchParams['destination'];
        $departureDate = $searchParams['departure_date'];
        $numberOfResults = $searchParams['number_of_results'] ?? 100;
        $returnDate = $searchParams['return_date'] ?? '';
        $cacheKey = 'amadeus_flight_search' . $origin . $destination . $numberOfResults . $departureDate. $returnDate;
        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }

        try {
            $client = new \GuzzleHttp\Client();
            $apiUrl = env('AMADEUS_FLIGHT_API_URL').'?apikey='
                .env('AMADEUS_API_KEY').'&number_of_results='.$numberOfResults.'&'.http_build_query($searchParams);
            $res = $client->request('GET', $apiUrl);
            $result['status'] = $res->getStatusCode();
            $data = json_decode($res->getBody()->getContents(),true);
            $result['data'] = $this->prepareResponse($data['results']);
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

    private function prepareResponse($data)
    {
        $result = [];
        $itineraries = [];
        foreach ($data as $key => $value) {
            foreach ($value['itineraries'] as $key => $itinerary) {
                $itineraries[$key][$itinerary['outbound']['flights'][0]['marketing_airline']] = $itinerary;
            }
            $result[$key]['itineraries'] = $itineraries;
            $result[$key]['fare'] = $value['fare'];
        }
        return $result;
    }
}

