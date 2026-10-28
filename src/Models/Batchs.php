<?php declare(strict_types=1);

namespace Api\Models;


use Ramsey\Uuid\Uuid;
use Phalcon\Mvc\Model;


class Batchs extends Model
{
    public $id;
    public $total;
    public $sold;
    public $reserved;
    public $available;


    public function initialize()
    {
        $this->setSource('batchs');
    }

    public function beforeValidationOnCreate()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function columnMap() 
    {
        return [
            'id' => 'id',
            'total' => 'total',
            'sold' => 'sold',
            'reserved' => 'reserved',
            'available' => 'available'
        ];
    }
}
