<?php

namespace App\Http\Controllers;

use App\User;
Use App\LoginAttempt;
use App\Jobs\UserRegisteredSendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Cache\RateLimiter;
use Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $maxAttempts = 5; // Default is 5
    protected $decayMinutes = 3; // Default is 1
    public function __construct(){
            
   
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return response()->json(['message' => 'Please Check Input and try again.'],400);
        }

        if(User::where('email',$request->email)->exists()){
            return response()->json(['message' => 'Email already taken.'],400);
        }


    
        $user = User::create([
            'name' =>   $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        
        UserRegisteredSendEmail::dispatch($user);

        $token = $user->createToken('Laravel Personal Access Client')->accessToken;

        $response = [
            'message' => "User Successfully Registered."
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request){
            $failedLogin = true;
            $message = '';
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if(!$validator->fails()){
               $failedLogin = false; 
            }

            if(User::where('email',$request->email)->exists()){
                $user = User::where('email', $request->email)->first();
                if(Hash::check($request->password, $user->password)){
                    $token = $user->createToken('Laravel Personal Access Client')->accessToken;

                    return response()->json([
                        'access_token' => $token
                    ], 201);
                }{
                    $failedLogin = true;
                    $message = 'Bad Credentials.';
                }
            }else{
                $failedLogin = true;
                $message = 'User do not exist.';
            }


            $limiter = app(RateLimiter::class);
            if ($user = $request->user()) {
                $key = sha1($user->getAuthIdentifier());
            }
    
            if ($route = $request->route()) {
                $key = sha1($route->getDomain().'|'.$request->ip());
            }
            $remaining = $limiter->retriesLeft($key, $this->maxAttempts);

            if($failedLogin){
                return response()->json(['message' => $message.' '.$remaining.' Attempts Remaining'],401);
            }
    }
}
