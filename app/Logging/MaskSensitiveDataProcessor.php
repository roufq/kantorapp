<?php

namespace App\Logging;

use App\Support\Security\DataMasker;

class MaskSensitiveDataProcessor
{
    public function __invoke(array $record): array
    {
        $record['context'] = DataMasker::mask($record['context'] ?? []);
        $record['extra'] = DataMasker::mask($record['extra'] ?? []);

        if (isset($record['message'])) {
            $record['message'] = DataMasker::maskString($record['message']);
        }

        return $record;
    }
}
