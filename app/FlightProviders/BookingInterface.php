<?php

namespace App\FlightProviders;

//@todo basic interface now. it needs to be changed amid real implementation of booking API
interface BookingInterface
{
    /**
     * create new booking request
     *
     * @param array $data
     * @return mixed
     */
    public function createBooking(array $data);

    /**
     * cancel booking interface
     *
     * @param mixed $bookingInfo
     * @return mixed
     */
    public function cancelBooking($bookingInfo);

    /**
     * update booking request
     *
     * @param int $id
     * @param array $inputData
     * @return mixed
     */
    public function updateBooking($id, array $inputData = []);

}
