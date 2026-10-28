<?php declare(strict_types=1);


namespace Api\Controllers;

use Api\Dto\WebhookPaymentDTO;


class WebhookController extends BaseController
{

    public function indexAction()
    {
        $webhookPaymentDTO = new WebhookPaymentDTO($this->request->getJsonRawBody());
        $webhookPaymentDTO->validate();

        $webhookRepository = $this->di->getShared('webhookRepository');
        return $this->json($webhookRepository->handlerStatus($webhookPaymentDTO), 200);
    }

}
