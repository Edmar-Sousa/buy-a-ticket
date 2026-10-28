<?php declare(strict_types=1);

namespace Api\Dto;


use Api\Exceptions\ValidationException;
use stdClass;

use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Filter\Validation\Validator\Callback;


class CreateReservationDTO 
{

    public string $cpf;

    public int $quantity;


    public function __construct(stdClass $body) 
    {
        $this->cpf = $body->cpf ?? '';
        $this->quantity = $body->quantity ?? 0;
    }

    private function makeValidate() 
    {
        $validator = new Validation();

        $validator->add('cpf', new StringLength([
            'max' => 11,
            'min' => 11,
            'messageMaximum' => 'O CPF deve ter 11 dígitos.',
            'messageMinimum' => 'O CPF deve ter 11 dígitos.',
        ]));

        $validator->add('quantity', new Callback([
            'callback' => fn ($data) => 
                isset($data['quantity']) && 
                is_numeric($data['quantity']) && 
                $data['quantity'] >= 1,
            'message' => 'O valor deve ser de pelo menos um'
        ]));

        return $validator;
    }

    public function validate()
    {
        $validator = $this->makeValidate();
        $validated = $validator->validate([
            'cpf' => $this->cpf, 
            'quantity' => $this->quantity,
        ]);
        
        if (count($validated) > 0)
        {
            $errors = [];

            foreach ($validated as $message) {
                $errors[$message->getField()][] = $message->getMessage();
            }

            throw new ValidationException($errors);
        }
        
    }
}

