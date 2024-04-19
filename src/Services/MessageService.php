<?php

namespace App\Services;

class MessageService implements MessageServiceInterface
{
    public function getMessage(): string
    {
        return "Hello, this is your message service speaking!";
    }
}