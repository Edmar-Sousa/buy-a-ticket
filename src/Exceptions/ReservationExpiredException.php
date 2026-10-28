<?php declare(strict_types=1);

namespace Api\Exceptions;



class ReservationExpiredException extends CustomException
{

    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Reserva expirada", 
            410,
            'RESERVATION_EXPIRED',
            $errors
        );
    }

}