<?php

namespace App\Http\Controllers;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            return response()->json([
                'status' => 'success',
                'msg' => "Register successfully!"
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            \Log::error('User registration failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => request('email'),
            'password' => request('password')
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('token', $token, 60 * 24);
            if ($user->categories) {
                $flag = true;
            }
            return response()->json([
                'status' => 'success',
                'user' => $user,
                "token" => $token
            ], Response::HTTP_OK)
                ->withCookie($cookie);
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => 'Invalid Credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }


    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();

            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'status' => 'success',
                'msg' => 'Logout successful',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Logout failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'failed',
                'msg' => 'Logout failed. Please try again.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
