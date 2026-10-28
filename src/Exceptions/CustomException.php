<?php declare(strict_types=1);

namespace Api\Exceptions;

use Exception;


class CustomException extends Exception 
{

    protected string $statusText;
    protected mixed $errors;

    
    public function __construct(
        string $message = '', 
        int $errorCode = 500, 
        string $statusText = 'INTERNAL_ERROR', 
        mixed $errors = []
    )
    {
        parent::__construct($message, $errorCode);

        $this->statusText = $statusText;
        $this->errors = $errors;
    }

    final public function getStatusText() 
    {
        return $this->statusText;
    }


    final public function getErrors() 
    {
        return $this->errors;
    }

    final public function hasErrors() 
    {
        return count($this->errors) > 0;
    }
}
