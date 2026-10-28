<?php declare(strict_types=1);

namespace Api\Exceptions;



class NotFoundReservationException extends CustomException
{

    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Reserva não encontrada", 
            400,
            'NOT_FOUND_RESERVATION',
            $errors
        );
    }

}