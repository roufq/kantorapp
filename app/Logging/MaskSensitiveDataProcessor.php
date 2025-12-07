<?php

namespace App\Logging;

use App\Support\Security\DataMasker;
use Monolog\LogRecord;

class MaskSensitiveDataProcessor
{
    /**
     * Handle Monolog record (supports array for Monolog v2 and LogRecord for v3).
     */
    public function __invoke($record)
    {
        $isObject = $record instanceof LogRecord;

        $context = $isObject ? $record->context : ($record['context'] ?? []);
        $extra = $isObject ? $record->extra : ($record['extra'] ?? []);
        $message = $isObject ? $record->message : ($record['message'] ?? null);

        $context = DataMasker::mask($context ?? []);
        $extra = DataMasker::mask($extra ?? []);
        if ($message !== null) {
            $message = DataMasker::maskString($message);
        }

        if ($isObject) {
            return $record->with(context: $context, extra: $extra, message: $message ?? '');
        }

        $record['context'] = $context;
        $record['extra'] = $extra;
        if ($message !== null) {
            $record['message'] = $message;
        }

        return $record;
    }
}
