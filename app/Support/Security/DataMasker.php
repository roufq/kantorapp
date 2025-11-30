<?php

namespace App\Support\Security;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DataMasker
{
    /**
     * Mask sensitive values in arrays or scalars using configured keys/patterns.
     */
    public static function mask(mixed $value, array $forceKeys = []): mixed
    {
        if (is_array($value)) {
            return static::maskArray($value, $forceKeys);
        }

        if (is_object($value)) {
            return $value;
        }

        return static::maskString($value, $forceKeys);
    }

    public static function maskArray(array $data, array $forceKeys = []): array
    {
        $sensitiveKeys = static::sensitiveKeys($forceKeys);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = static::maskArray($value, $forceKeys);
                continue;
            }

            if (is_object($value)) {
                $data[$key] = $value;
                continue;
            }

            $lowerKey = Str::lower((string) $key);
            $forceMask = in_array($lowerKey, $sensitiveKeys, true);
            if ($forceMask) {
                $data[$key] = static::maskString($value, $forceKeys, true);
                continue;
            }

            $data[$key] = static::maskString($value, $forceKeys);
        }

        return $data;
    }

    public static function maskString(mixed $value, array $forceKeys = [], bool $force = false): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return $trimmed;
        }

        if ($force) {
            return static::maskGeneric($trimmed);
        }

        // Mask inline emails and numbers while preserving surrounding text.
        $masked = preg_replace_callback(
            '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}/i',
            fn ($match) => static::maskEmail($match[0]),
            $trimmed
        );

        $masked = preg_replace_callback(
            config('security.masking.patterns.phone'),
            fn ($match) => static::maskDigits($match[0]),
            $masked
        );

        $masked = preg_replace_callback(
            config('security.masking.patterns.national_id'),
            fn ($match) => static::maskDigits($match[0]),
            $masked
        );

        if (static::looksLikeToken($trimmed)) {
            return static::maskGeneric($masked);
        }

        return $masked;
    }

    protected static function maskEmail(string $email): string
    {
        [$user, $domain] = explode('@', $email, 2);
        $maskedUser = static::maskSegment($user);
        $maskedDomain = static::maskSegment($domain, preserveSeparator: '.');

        return "{$maskedUser}@{$maskedDomain}";
    }

    protected static function maskDigits(string $value): string
    {
        $digits = preg_replace('/\\D+/', '', $value) ?? '';
        if (strlen($digits) <= 4) {
            return str_repeat('*', strlen($digits));
        }

        $visibleEnd = substr($digits, -2);
        return str_repeat('*', max(strlen($digits) - 2, 0)) . $visibleEnd;
    }

    protected static function maskGeneric(string $value): string
    {
        $length = strlen($value);
        if ($length <= 6) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 3) . str_repeat('*', $length - 5) . substr($value, -2);
    }

    protected static function maskSegment(string $value, string $preserveSeparator = null): string
    {
        if ($preserveSeparator && str_contains($value, $preserveSeparator)) {
            $parts = explode($preserveSeparator, $value);
            $parts = array_map(fn ($part) => static::maskSegment($part), $parts);
            return implode($preserveSeparator, $parts);
        }

        if (strlen($value) <= 2) {
            return str_repeat('*', strlen($value));
        }

        return substr($value, 0, 1) . str_repeat('*', max(strlen($value) - 2, 0)) . substr($value, -1);
    }

    protected static function sensitiveKeys(array $forceKeys = []): array
    {
        $configured = Arr::wrap(config('security.masking.keys', []));
        return array_values(array_unique(array_map('strtolower', array_merge($configured, $forceKeys))));
    }

    protected static function looksLikeToken(string $value): bool
    {
        $minLength = (int) config('security.masking.token_min_length', 16);
        $hasLettersAndNumbers = preg_match('/[a-z]/i', $value) && preg_match('/\\d/', $value);

        return $hasLettersAndNumbers && strlen($value) >= $minLength;
    }
}
