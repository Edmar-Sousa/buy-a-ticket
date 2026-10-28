<?php declare(strict_types=1);


namespace Api\Repository;


use Phalcon\Messages\MessageInterface;


class Repository 
{
    final function handlerErrors(MessageInterface $messages) 
    {
        $errors = [];

        foreach ($messages as $message) 
            $errors[$message->getField()][] = $message->getMessage();

        return $errors;
    }
}
