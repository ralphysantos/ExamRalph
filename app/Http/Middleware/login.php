<?php

namespace App\Http\Middleware;
use \Illuminate\Routing\Middleware\ThrottleRequests;
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
    public function handle($request, Closure $next,$maxAttempts = 5, $decayMinutes = 300)
    {
        $key = parent::resolveRequestSignature($request);
        $attempts = parent::calculateRemainingAttempts($key, $maxAttempts);

        if($attempts > 1 ){
            return response()->json(['message' => 'Bad Credentials.'.$attempts.' Attempts Remaining'],401);
        }
        if($this->limiter->tooManyAttempts($key,$maxAttempts,$decayMinutes)){
            $secs = parent::getTimeUntilNextRetry($key);
            return response()->json(['message' => 'Bad Credentials. User locked. Time Remaining '.gmdate("H:i:s", $secs)],401); 
        }
        return $next($request);
    }
}
