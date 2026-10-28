<?php declare(strict_types=1);

namespace Api\Repository;


use Phalcon\Di\Di;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Mvc\Model\TransactionInterface;

use Api\Models\Batchs;

use Throwable;


class BatchRepository 
{

    public function getBatch() 
    {
        /**
         * @var Batchs
         */
        $batch = Batchs::findFirst();

        return [
            'total' => $batch->total,
            'sold' => $batch->sold,
            'reserved' => $batch->reserved,
            'available' => $batch->available,
        ];
    }


    private function resetFirstBatch(TransactionInterface & $tx)
    {
        /**
         * @var Batchs
         */
        $batch = Batchs::findFirst([
            'transaction' => $tx,
        ]);

        $batch->sold = 0;
        $batch->reserved = 0;
        $batch->available = 100;

        $batch->update();

        return $batch;
    }

    private function resetTickets(TransactionInterface & $tx)
    {
        $di = Di::getDefault();
        $modelsManager = $di->get('modelsManager');

        $modelsManager->executeQuery(
            'DELETE FROM Api\Models\Reservations',
            [],
            ['transaction' => $tx]
        );

    }

    public function resetBatch()
    {
        $txManager = new TxManager();
        $transaction = $txManager->get();


        try {
            $batch = $this->resetFirstBatch($transaction);
            $this->resetTickets($transaction);

            $transaction->commit();
            return [
                'total' => $batch->total,
                'sold' => $batch->sold,
                'reserved' => $batch->reserved,
                'available' => $batch->available,
            ];
        }

        catch (Throwable $err) {
            $transaction->rollback();
            throw $err;
        }

    }
}
