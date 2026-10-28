<?php declare(strict_types=1);

namespace Api\Repository;


use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Mvc\Model\TransactionInterface;

use Api\Dto\CreateReservationDTO;
use Api\Exceptions\SoldOutException;
use Api\Exceptions\MaxTicketsByCpfException;
use Api\Models\Batchs;
use Api\Models\Reservations;

use Throwable;
use DateTimeImmutable;
use DateTimeZone;


class ReservationRepository extends Repository
{


    private function updateBatch(CreateReservationDTO & $dto, TransactionInterface & $tx)
    {
        /**
         * @var Batchs
         */
        $batch = Batchs::findFirst([
            'transaction' => $tx,
            'for_update' => true,
        ]);

        if ($dto->quantity > $batch->available)
            throw new SoldOutException();

        $batch->reserved += $dto->quantity;
        $batch->available -= $dto->quantity;

        $batch->update();
    }


    private function newReservation(CreateReservationDTO & $dto, TransactionInterface & $tx)
    {
        $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->modify('+5 minutes');

        $reservation = new Reservations([
            'quantity' => $dto->quantity,
            'cpf' => $dto->cpf,
            'status' => 'RESERVED',
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s\Z')
        ]);

        $reservation->setTransaction($tx);
        $reservation->save();

        return $reservation;
    }


    private function verifyReservationByCpf(CreateReservationDTO & $dto, TransactionInterface & $tx)
    {
        if ($dto->quantity > 2)
            throw new MaxTicketsByCpfException();


        /**
         * @var Reservations
         */
        $reservation = Reservations::findFirst([
            'conditions' => 'cpf = :cpf:',
            'bind' => [ 'cpf' => $dto->cpf ],
            'transaction' => $tx,
            // 'for_update' => true,
        ]);

        if ($reservation && $reservation->quantity + $dto->quantity > 2) 
            throw new MaxTicketsByCpfException();


        if (!$reservation) 
            $reservation = $this->newReservation($dto, $tx);
        else 
        {
            $reservation->quantity += $dto->quantity;
            $reservation->update();
        }

        return $reservation;
    }

    public function createReservation(CreateReservationDTO $dto) 
    {
        $txManager = new TxManager();
        $transaction = $txManager->get();

        try 
        {
            $reservation = $this->verifyReservationByCpf($dto, $transaction);
            $this->updateBatch($dto, $transaction);

            $transaction->commit();

            return [
                'reservation_id' => $reservation->id,
                'cpf' => $reservation->cpf,
                'quantity' => $reservation->quantity,
                'status' => $reservation->status,
                'expires_at' => $reservation->expiresAt,
            ];
        }

        catch (Throwable $err) 
        {
            $transaction->rollback();
            throw $err;
        }

    }

}
