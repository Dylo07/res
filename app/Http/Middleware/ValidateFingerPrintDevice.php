<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateFingerPrintDevice
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Device-API-Key');
        
        if (!$apiKey || !$this->isValidDeviceKey($apiKey)) {
            return response()->json([
                'error' => 'Unauthorized device'
            ], 401);
        }
        
        return $next($request);
    }
    
    private function isValidDeviceKey($key)
    {
        return in_array($key, config('fingerprint.allowed_devices'));
    }
}