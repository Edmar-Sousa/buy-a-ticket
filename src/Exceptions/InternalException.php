<?php declare(strict_types=1);


namespace Api\Exceptions;



class InternalException extends CustomException
{
    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Erro de interno", 
            500,
            'INTERNAL_ERROR',
            $errors
        );
    }
}

