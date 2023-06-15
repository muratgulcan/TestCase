<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hatalı giriş',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'Başarıyla giriş yapıldı',
                'token' => $user->createToken("case-token")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string',
            ]);

            $user = User::create([
                'name' => $validate['name'],
                'email' => $validate['email'],
                'password' => bcrypt($validate['password'])
            ]);

            $token = $user->createToken('case-token')->plainTextToken;

            return response()->json([
                'status' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }


    public function logout()
    {
        try {
            Auth::user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return response()->json('Başarıyla çıkış yapıldı');
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            if (Auth::user()->role_id == 1) {
                $user = User::find($id);

                if (!$user) {
                    return response()->json(['message' => 'Kullanıcı bulunamadı'], 404);
                }

                $user->role_id = $request->input('role_id') ?? $user->role_id;
                $user->save();

                return response()->json([
                    'status' => true,
                    'user' => $user
                ], 200);
            } else {
                return response()->json(['message' => 'Yetkiniz yok'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'SERVER ERROR'
            ], 500);
        }
    }
}
