<?php

namespace App\Services;

use Stripe\Checkout\Session as StripeSession;

class StripeSessionWrapper
{
    public function create(array $params)
    {
        return StripeSession::create($params);
    }
}
