<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class AirportsTransformer extends TransformerAbstract
{

    public function transform(array $airport) : array
    {
        return [
            'label' => $airport['label'],
            'value' => $airport['value'],
        ];
    }
}
