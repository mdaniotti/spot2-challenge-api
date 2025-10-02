<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class SanitizeMongoInput
{
  
    // Handle an incoming request.
    public function handle(Request $request, Closure $next)
    {
        $allInput = array_merge($request->all(), $request->query());
        
        if ($this->hasMaliciousKeys($allInput)) {
            abort(400, "Invalid request: MongoDB operators not allowed.");
        }

        return $next($request);
    }

    // Check if the input contains dangerous keys.
    protected function hasMaliciousKeys(array $input): bool
    {
        foreach ($input as $key => $value) {
            // Detect claves that start with "$" or contain "."
            if (is_string($key) && (str_starts_with($key, '$') || str_contains($key, '.'))) {
                return true;
            }

            // Recursive for nested arrays
            if (is_array($value) && $this->hasMaliciousKeys($value)) {
                return true;
            }
        }

        return false;
    }
}
