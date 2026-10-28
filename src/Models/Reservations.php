<?php declare(strict_types=1);

namespace Api\Models;


use Ramsey\Uuid\Uuid;
use Phalcon\Mvc\Model;


class Reservations extends Model
{
    public $id;
    public $quantity;
    public $cpf;
    public $status;
    public $expiresAt;


    public function initialize()
    {
        $this->setSource('reservations');
    }

    public function beforeValidationOnCreate()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function columnMap() 
    {
        return [
            'id' => 'id',
            'quantity' => 'quantity',
            'cpf' => 'cpf',
            'status' => 'status',
            'expires_at' => 'expiresAt',
        ];
    }
}
