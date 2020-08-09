<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error: '.$validator->errors(),
            ];

            return response()->json($response, 400);
        }

        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            $scope = $user->admin ? 'admin' : 'user';

            $accessToken = $user->createToken('Demo', [$scope])->accessToken;

            $response = [
                'success' => true,
                'token' => $accessToken,
            ];

            if ($user->admin) {
                $response['admin'] = true;
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function login(Request $request)
    {
        // Double check the maximum number of attempts
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $scope = $user->admin ? 'admin' : 'user';

            $accessToken = $user->createToken('Demo', [$scope])->accessToken;

            $response = [
                'success' => true,
                'token' => $accessToken,
                'data' => $user,
            ];

            if ($user->admin) {
                $response['admin'] = true;
            }

            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Email and/or Password is incorrect',
            ];

            return response()->json($response, 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'newPassword' => 'required|min:6',
            'c_password' => 'required|same:newPassword',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error: '.$validator->errors(),
            ];

            return response()->json($response, 400);
        }

        try {
            $user = Auth::user();

            if ((Hash::check(request('password'), $user->password)) == false) {
                throw new \Exception('Old password is incorrect');
            }

            if ((Hash::check(request('newPassword'), $user->password)) == true) {
                throw new \Exception('New password is too similar to existing');
            }

            $user->update(['password' => Hash::make(request('newPassword'))]);

            $response = [
                'success' => true,
                'result' => "Password updated successfully.",
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }
}