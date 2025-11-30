<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
    * Handle an incoming request.
    */
    public function handle(Request $request, Closure $next)
    {
        $skipKeys = config('security.sanitization.skip_keys', []);

        $sanitized = $this->sanitizeArray($request->all(), $skipKeys);
        $request->merge($sanitized);

        return $next($request);
    }

    private function sanitizeArray(array $data, array $skipKeys): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value, $skipKeys);
                continue;
            }

            if (is_string($value)) {
                // Trim, strip tags, collapse multiple spaces
                $clean = trim(strip_tags($value));
                $clean = preg_replace('/\s+/', ' ', $clean);
                $data[$key] = $clean;
            }
        }

        return $data;
    }
}
