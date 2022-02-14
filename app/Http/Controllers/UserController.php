<?php

namespace App\Http\Controllers;

use App\User;
Use App\LoginAttempt;
use App\Jobs\UserRegisteredSendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Carbon\Carbon;
class UserController extends Controller
{

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

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            // $loginattempt = LoginAttempt::firstOrCreate([
            //     'ip'=> isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'127.0.0.1',
            //     'email' => $request->email,
            // ]);

            // if($loginattempt->attempt >= 4){
            //     if(empty($loginattempt->lock_time)){
            //         $loginattempt->lock_time = Carbon::now()->addSeconds(300)->toDateTimeString();
            //         $loginattempt->save();
            //     }
            //     $lock = Carbon::parse($loginattempt->lock_time);
            //     $now = Carbon::now();
            //     $time_remaining = $lock->diff($now);

            //     if($lock->diffInSeconds($now) < 300){
            //         return response()->json(['message' => 'Bad Credentials. User locked. Time Remaining '.$time_remaining->format('%H:%i:%s')],401);
            //     }else{
            //         $loginattempt->lock_time = null;
            //         $loginattempt->attempt = 0;
            //         $loginattempt->save();
            //     }
            // }

            if($validator->fails()){
                // $loginattempt->attempt = $loginattempt->attempt + 1;
                // $loginattempt->save();

                // $remain = 5 - $loginattempt->attempt;
                // return response()->json(['message' => 'Bad Credentials.'.$remain.' Attempts Remaining'],401);
                return response()->json(['message' => 'Bad Credentials.'],401);
            }else{
                $loginattempt->delete();
            }
            if(User::where('email',$request->email)->exists()){
                $user = User::where('email', $request->email)->first();
                if(Hash::check($request->password, $user->password)){
                    $token = $user->createToken('Laravel Personal Access Client')->accessToken;

                    return response()->json([
                        'access_token' => $token
                    ], 201);
                }{
                    return response()->json(['message' => 'Bad Credentials.'],401);
                }
            }else{
                return response()->json(['message' => 'User do not exist.'],401);
            }
    }
}
