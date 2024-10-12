<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    use ResponseTrait;

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $user = User::where('id', auth()->id())->first();
            $success['token'] = $token;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['role'] = $user->role;

            return $this->success($success, 'User has been loggedIn successfully.');
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return  $this->error('validation.', $message);
        }

        $data = $request->all();

        $data['password'] = Hash::make($request->password);

        $record = User::create($data);
        $token = JWTAuth::fromUser($record);
        $record['token'] = $token;
        return $this->success($record, 'User has been created successfully');
    }
}
