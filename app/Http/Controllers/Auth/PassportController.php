<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassportController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'response_code' => 201,
            'message' => 'Success',
            'token' => $token
        ], 200);
    }

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('Personal Access Token')->accessToken;
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'expires_at' => ''
                    ],
                    'user' => Auth::user()
                ]
            ], 200);
        } else {
            return response()->json([
                'response_code' => 401,
                'message' => 'Unauthorised',
            ]);
        }
    }

    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}
