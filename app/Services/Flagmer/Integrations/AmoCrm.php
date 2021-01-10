<?php


namespace App\Services\Flagmer\Integrations;


use App\Services\Flagmer\Integrations\Amocrm\sendLeadDto;

class AmoCrm
{
    public function sendLeadAction(sendLeadDto $lead): void
    {
        echo "Sending lead {$lead->lead_id} to AmoCRM...";
        sleep(random_int(1,3));
        echo "Done\n";
    }
}
