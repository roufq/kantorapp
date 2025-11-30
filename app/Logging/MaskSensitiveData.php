<?php

namespace App\Logging;

use Illuminate\Log\Logger;

class MaskSensitiveData
{
    public function __invoke(Logger $logger): void
    {
        $logger->pushProcessor(new MaskSensitiveDataProcessor());
    }
}
