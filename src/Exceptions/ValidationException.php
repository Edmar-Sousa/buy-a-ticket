<?php declare(strict_types=1);


namespace Api\Exceptions;

use Api\Exceptions\CustomException;


class ValidationException extends CustomException
{


    public function __construct(mixed $errors = [])
    {
        parent::__construct(
            "Erro de validação", 
            400,
            'VALIDATION',
            $errors
        );
    }


}

