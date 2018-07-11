<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;

class Controller extends BaseController
{
    use ResponseTrait;

    /**
     * Constructor
     *
     * @param Manager|null $fractal
     */
    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal === null ? new Manager() : $fractal;
        $this->setFractal($fractal);
        $this->validateRequest();
    }

    private function validateRequest()
    {
        if (!empty($this->validatorName)) {
            $this->middleware('validate:' . $this->validatorName, ['only' => [
                'store',
                'update',
                'index'
            ]]);
        }
    }

    public function createPagination(array $data, $page=1, $paginate = 10)
    {
        $offSet = ($page * $paginate) - $paginate;
        $itemsForCurrentPage = array_slice($data, $offSet, $paginate, true);
        return new LengthAwarePaginator($itemsForCurrentPage, count($data), $paginate, $page);
    }
}
