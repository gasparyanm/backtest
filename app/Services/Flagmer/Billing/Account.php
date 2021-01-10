<?php


namespace Flagmer\Billing;


use Flagmer\Billing\Account\processPaymentDto;

class Account
{
    public function processPaymentAction(processPaymentDto $input): void
    {
        echo "Processing payment for {$input->account_id} with {$input->amount} amount...";
        sleep(random_int(1,3));
        echo "Done\n";
    }

}