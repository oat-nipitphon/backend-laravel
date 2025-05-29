<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with([
                'user_status',
                'user_wallet',
                'user_profile',
                'report_log_logins',
                'check_status_login'
            ])
                ->get()
                ->map(function ($user) {
                    return $user;
                });

            if (empty($users)) {
                return response()->json([
                    'message' => "laravel get user false",
                    'users' => $users
                ], 404);
            }

            return response()->json([
                'message' => "laravel get user success",
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel get user function error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $users = User::with([
                'user_status',
                'user_wallet',
                'user_profile',
                'report_log_logins',
                'check_status_login'
            ])
                ->findOrFail($id);

            if (empty($users)) {
                return response()->json([
                    'message' => "laravel get show user false",
                    'users' => $users
                ], 404);
            }

            return response()->json([
                'message' => "laravel get show user success",
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel get show user function error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $request->validate([
                'userID' => 'required|integer',
                'name' => 'required|string',
                'email' => 'required|string',
                'username' => 'required|string',
                'password' => 'required|string', //Hash::make($request->password)
                'statusID' => 'required|integer',
            ]);

            $user = User::findOrFail($id);

            if (empty($user)) {
                return response()->json([
                    'message' => "laravel update user where id false",
                    'userID' => $id
                ], 404);
            }

            $user->update([
                'name' => $request->userID,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'status_id' => $request->statusID,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => "laravel update user success.",
                'user' => $user
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel update user function error",
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
