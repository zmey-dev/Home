<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\EmployeeCreated;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $employee = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'employee'
            ]);
            $employee->notify(new EmployeeCreated());
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
}
