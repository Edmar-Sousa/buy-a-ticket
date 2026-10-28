<?php declare(strict_types=1);

namespace Api\Repository;

use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Mvc\Model\TransactionInterface;

use Api\Exceptions\NotFoundReservationException;
use Api\Exceptions\ReservationExpiredException;
use Api\Models\Batchs;
use Api\Dto\WebhookPaymentDTO;
use Api\Models\Reservations;

use Throwable;
use DateTimeImmutable;
use DateTimeZone;


class WebhookRepository
{

    private function getReservation(WebhookPaymentDTO & $dto, TransactionInterface & $tx) 
    {
        /**
         * @var Reservations|null
         */
        $reservation = Reservations::findFirst([
            'conditions' => 'id = :id: AND status = :status:',
            'bind' => [ 
                'id' => $dto->reservationId, 
                'status' => 'RESERVED'
            ],
            'transaction' => $tx,
        ]);

        if (!$reservation)
            throw new NotFoundReservationException();

        return $reservation;
    }


    private function successSold(WebhookPaymentDTO & $dto, TransactionInterface & $tx)
    {
        $reservation = $this->getReservation($dto, $tx);

        $reservationExpires = new DateTimeImmutable($reservation->expiresAt, new DateTimeZone('UTC'));
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        if ($reservationExpires < $now)
            throw new ReservationExpiredException();

        $reservation->status = 'SOLD';
        $reservation->update();

        return $reservation;
    }

    private function getBatchToUpdate(TransactionInterface & $tx) 
    {
        /**
         * @var Batchs|null
         */
        $batch = Batchs::findFirst([
            'transaction' => $tx,
            'for_update' => true,
        ]);

        return $batch;
    }

    private function deleteReservation(Reservations & $reservation)
    {
        $reservation->delete();
    }

    private function updateSoldBatch(Reservations & $reservation, TransactionInterface & $tx)
    {
        $batch = $this->getBatchToUpdate($tx);

        $batch->sold += $reservation->quantity;
        $batch->reserved -= $reservation->quantity;

        $batch->update();
        return $batch;
    }

    private function updateAvailableBatch(Reservations & $reservation, TransactionInterface & $tx)
    {
        $batch = $this->getBatchToUpdate($tx);

        $batch->reserved -= $reservation->quantity;
        $batch->available += $reservation->quantity;

        $batch->update();
        return $batch;
    }


    private function successTransaction(WebhookPaymentDTO & $dto)
    {
        $txManager = new TxManager();
        $transaction = $txManager->get();

        try 
        {
            $reservation = $this->successSold($dto, $transaction);
            $this->updateSoldBatch($reservation, $transaction);

            $transaction->commit();

            return [
                'reservation_id' => $reservation->id,
                'status' => 'SOLD',
            ];
        }
        catch (ReservationExpiredException $err) 
        {
            $this->updateAvailableBatch($reservation, $transaction);
            $this->deleteReservation($reservation);

            throw $err;
        }
        catch (Throwable $err) 
        {
            $transaction->rollback();
            throw $err;
        }
    }


    private function failedTransaction(WebhookPaymentDTO & $dto)
    {
        $txManager = new TxManager();
        $transaction = $txManager->get();

        try 
        {
            $reservation = $this->getReservation($dto, $transaction);

            $this->updateAvailableBatch($reservation, $transaction);
            $this->deleteReservation($reservation);

            $transaction->commit();

            return [
                'reservation_id' => $reservation->id,
                'status' => 'RELEASED',
            ];
        }

        catch (Throwable $err)
        {
            $transaction->rollback();
            throw $err;
        }
    }

    public function handlerStatus(WebhookPaymentDTO $dto) 
    {
        if ($dto->status == 'SUCCESS') 
            return $this->successTransaction($dto);
        
        return $this->failedTransaction($dto);
    }

}
