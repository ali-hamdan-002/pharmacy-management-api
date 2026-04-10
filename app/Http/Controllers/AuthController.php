<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

use Carbon\Carbon;
//use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Password as Password_rule;




class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:55',
            'pharmacy_name' => 'required|string|max:255',
            'phone'         => 'required|string|unique:users,phone|min:10',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'          => $request->name,
            'pharmacy_name' => $request->pharmacy_name,
            'phone'         => $request->phone,
            'password'      => $request->password, 
        ]);
        $accessToken = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'success' => true,
            'user'    => $user,
            'access_token' => $accessToken,
            'message' => 'User registered successfully'
        ], 201);
    }


    public function login(Request $request)
    {
        $loginData = $request->validate( [
            'phone'    => 'required',
            'password' => 'required'
        ]);

      if (!auth()->attempt(['phone' => $request->phone, 'password' => $request->password])) {
        return response()->json([
            'errors' => [
                'message' => ['Could not sign you in with those credentials']
            ]
        ], 422);
        }
        $user = auth()->user(); 
        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeek(1);
            }

        $token->save();

        return response()->json([
            'success'      => true,
            'user'         => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    public function refresh()
    {
        $user = Auth::user();
        $user->token()->revoke();
        $newToken = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'user' => $user,
            'authorisation' => [
                'token' => $newToken,
                'type' => 'Bearer',
            ]
        ]);
    }
    public function deteails(){}

  

}
