<?php declare(strict_types=1);


namespace Api\Exceptions;



class SoldOutException extends CustomException
{
    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Erro de validação", 
            409,
            'SOLD_OUT',
            $errors
        );
    }
}

