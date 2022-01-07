<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use App\Jobs\SendNewRegistrationEmail;
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
    
            if($validator->fails()){
                return response()->json(['message' => 'Bad Credentials.'],401);
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
            }
    }
}
