<?php declare(strict_types=1);

namespace Api\Controllers;


use Api\Controllers\BaseController;
use Api\Dto\CreateReservationDTO;



class BatchController extends BaseController
{

    public function indexAction() 
    {
        $batchRepository = $this->di->getShared('batchRepository');
        return $this->json($batchRepository->getBatch(), 200);
    }

    public function resetAction() 
    {
        $batchRepository = $this->di->getShared('batchRepository');
        return $this->json($batchRepository->resetBatch(), 200);
    }

}