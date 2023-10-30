<?php

namespace App\Exceptions;

use Exception;

class PaymentsNotFoundException extends Exception
{
    public function report()
    {
        \Log::debug('Оплата не найдена!');
    }
}
