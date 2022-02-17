<?php

namespace App\Http\Middleware;
use \Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Carbon\CarbonInterval;
use Closure;

class login extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$maxAttempts = 5, $decayMinutes = 3,$prefix = '')
    {
        $key = $prefix.parent::resolveRequestSignature($request);
        $attempts = parent::resolveMaxAttempts($request, $maxAttempts);
        $limiter = $this->limiter->hit($key, $decayMinutes * 60);
        $remaining = parent::calculateRemainingAttempts($key, $attempts);
        if($remaining > 0 ){
            return response()->json(['message' => 'Bad Credentials.'.$remaining.' Attempts Remaining'],401);
        }
        $secs = parent::getTimeUntilNextRetry($key);
        if($this->limiter->tooManyAttempts($key,$attempts)){
            $secs = parent::getTimeUntilNextRetry($key);
            return response()->json(['message' => 'Bad Credentials. User locked. Time Remaining '.gmdate("H:i:s", $secs)],401); 
        }

        $response = $next($request);
    }
}

// $key = $prefix.$this->resolveRequestSignature($request);

// $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

// if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
//     throw $this->buildException($key, $maxAttempts);
// }

// $this->limiter->hit($key, $decayMinutes * 60);

// $response = $next($request);

// return $this->addHeaders(
//     $response, $maxAttempts,
//     $this->calculateRemainingAttempts($key, $maxAttempts)
// );