<?php declare(strict_types=1);

namespace Api\Dto;

use Api\Exceptions\ValidationException;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\InclusionIn;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;
use stdClass;


class WebhookPaymentDTO 
{
    public string $reservationId;
    public string $status;


    public function __construct(stdClass $body)
    {
        $this->reservationId = $body->reservation_id;
        $this->status = $body->status;
    }


    private function makeValidate()
    {
        $validation = new Validation();

        $validation->add('reservation_id', new StringLength([
            'max' => 36,
            'min' => 30,
            'messageMaximum' => 'O identificador deve ser um UUID',
            'messageMinimum' => 'O identificador deve ser um UUID',
        ]));

        $validation->add('status', new InclusionIn([
            'domain' => ['SUCCESS', 'FAILED'],
            'message' => 'O status deve ser SUCCESS ou FAILED'
        ]));

        return $validation;
    }


    public function validate() 
    {
        $validator = $this->makeValidate();

        $validated = $validator->validate([
            'reservation_id' => $this->reservationId, 
            'status' => $this->status,
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
