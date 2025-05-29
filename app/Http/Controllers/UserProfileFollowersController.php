<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfileFollowers;

class UserProfileFollowersController extends Controller
{

    public function followersProfile(Request $request, string $profileID, string $profileIDFollowers)
    {
        try {

            $followers = UserProfileFollowers::where('profile_id', $profileID)
                ->whereIn('profile_id_followers', $profileIDFollowers)
                ->first();

            $checkStatusFollowers = '';

            if (!empty($followers)) {

                if ($followers->status === 'true') {
                    $followers->delete();
                    $checkStatusFollowers = 'false';
                } else {
                    $followers->update([
                        'status' => 'true',
                        'updated_at' => now(),
                    ]);
                    $checkStatusFollowers = 'true';
                }

            } else {

                UserProfileFollowers::create([
                    'profile_id' => $profileID,
                    'profile_id_followers' => $profileIDFollowers,
                    'status' => 'true',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $checkStatusFollowers = 'true';

            }

            if (empty($checkStatusFollowers)) {

                return response()->json([
                    'message' => 'laravel user profile check status followers false',
                    'checkStatusFollowers' => $checkStatusFollowers
                ], 404);

            }

            return response()->json([
                'message' => 'laravel user profile followers success',
                'checkStatusFollowers' => $checkStatusFollowers
            ], 200);

        } catch (\Exception $error) {

            return response()->json([
                'message' => "laravel user profile followers function error",
                'error' => $error->getMessage()
            ], 500);

        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
