<?php declare(strict_types=1);


namespace Api\Exceptions;



class MaxTicketsByCpfException extends CustomException
{
    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Limite maximo por CPF", 
            422,
            'CPF_LIMIT_EXCEEDED',
            $errors
        );
    }
}

