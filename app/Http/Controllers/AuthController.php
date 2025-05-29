<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserLogLogin;
use App\Models\UserWallet;

class AuthController extends Controller
{

    // Register Check Email
    public function registerCheckEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    // Register Check Username
    public function registerCheckUsername(Request $request)
    {
        $exists = User::where('username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }

    // Register
    public function register(Request $request)
    {
        try {

            $validate = $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:3',
                'statusID' => 'required|integer',
            ]);

            $user = User::create([
                'name' => $validate['username'],
                'username' => $validate['username'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'status_id' => $validate['statusID'],
                'created_at' => now()
            ]);

            if (!empty($user)) {
                $userProfile = UserProfile::create([
                    'user_id' => $user->id,
                    'nick_name' => $validate['username'],
                    'created_at' => now()
                ]);

                $userWallet = UserWallet::create([
                    'user_id' => $user->id,
                    'point' => 0,
                    'status' => 'active',
                    'created_at' => now(),
                ]);

                $token = $user->createToken($user->username)->plainTextToken;

                return response()->json([
                    'message' => 'register successfully',
                    'user' => $user,
                    'userProfile' => $userProfile,
                    'userWallet' => $userWallet,
                    'token' => $token
                ], 201);
            }


            return response()->json([
                'message' => 'laravel function register response false'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "register function error",
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Login
    public function login(Request $request)
    {
        try {

            $request->validate([
                'emailUsername' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->emailUsername)
                ->orWhere('username', $request->emailUsername)
                ->first();

            if ($user || !Hash::check($request->password, $user->password)) {
                if (!empty($user)) {

                    $logLogin = UserLogLogin::create([
                        'user_id' => $user->id,
                        'status' => "online",
                        'time_in' => now(),
                        'created_at' => now(),
                    ]);

                    if (!empty($logLogin)) {

                        $token = $user->createToken($user->username)->plainTextToken;

                        return response()->json([
                            'message' => "Login successfullry",
                            'userLogin' => $user,
                            'LogLogin' => $logLogin,
                            'token' => $token,
                        ], 200);
                    }
                }
            }

            return response()->json([
                'message' => "laravel function login response false"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'login function error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        try {


            if ($user = $request->user()) {

                $LogLogout = UserLogLogin::where('user_id', $user->id)->first();


                if (empty($LogLogout) && empty($user)) {
                    return response()->json([
                        'message' => "Laravel function logout request user false"
                    ], 404);
                }

                $timeIn = \Carbon\Carbon::parse($LogLogout->time_in);
                $timeOut = now();
                $totalLogin = $timeIn->diffInSeconds($timeOut);

                $LogLogout->update([
                    'user_id' => $user->id,
                    'status' => "offline",
                    'time_out' => now(),
                    'time_total_login' => $totalLogin,
                    'updated_at' => now()
                ]);

                $user->tokens()->delete();

                return response()->json([
                    'message' => "logout successfullry.",
                    'logLogout' => $LogLogout,
                    'timeLogin' => $totalLogin,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'logout function error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
