<?php declare(strict_types=1);

namespace Api\Controllers;


use Api\Controllers\BaseController;
use Api\Dto\CreateReservationDTO;



class ReservationController extends BaseController
{


    public function indexAction() 
    {
        $createReservationDto = new CreateReservationDTO($this->request->getJsonRawBody());
        $createReservationDto->validate();


        $reservationRepository = $this->di->getShared('reservationRepository');

        return $this->json(
            $reservationRepository->createReservation($createReservationDto),
            201
        );
    }

}
