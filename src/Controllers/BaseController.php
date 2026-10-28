<?php declare(strict_types=1);

namespace Api\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Messages\Messages;
use Phalcon\Messages\Message;


class BaseController extends Controller 
{


    final public function json(mixed $content, int $status = 200) 
    {
        return $this->response
            ->setStatusCode($status)
            ->setJsonContent($content);
    }


    final public function errors(Messages $messages, string $type = 'VALIDATION', int $status = 400) 
    {
        $errors = [
            'error' => $type,
        ];

        foreach ($messages as $message) {
            $errors[$message->getField()][] = $message->getMessage();
        }

        return $this->response
            ->setStatusCode($status)
            ->setJsonContent($errors);
    }

}

