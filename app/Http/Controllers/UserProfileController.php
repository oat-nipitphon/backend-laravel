<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userProfiles = UserProfile::with([
                'profile_image',
                'profile_contacts',
                'profile_pops',
                'profile_followers',
                'posts'
            ])
                ->get()
                ->map(function ($profile) {
                    return $profile;
                });

            if (empty($userProfiles)) {

                return response()->json([
                    'message' => "laravel get user profile false.",
                    'user_profile' => $userProfiles
                ], 404);
            }

            return response()->json([
                'message' => "laravel get user profile success.",
                'user_profile' => $userProfiles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel get user profile function error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'profileID' => 'required|integer',
                'titleName' => 'required|string',
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'nickName' => 'required|string',
                'birthDay' => 'required|date',

            ]);


            $userProfile = UserProfile::findOrFail($validated['profileID']);

            if (empty($userProfile)) {
                return response()->json([
                    'message' => "laravel store user profile where id false",
                    'status' => false
                ], 404);
            }

            // format birth day
            $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');

            $userProfile->update([
                'title_name' => $validated['titleName'],
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'nick_name' => $validated['nickName'],
                'birth_day' => $birthDay,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'laravel store user profile success',
                'userProfile' => $userProfile,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'laravel store user profile function error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $userProfile = UserProfile::with([
                'profile_image',
                'profile_contacts',
                'profile_pops',
                'profile_followers',
                'posts'
            ])->findOrFail($id);

            if (empty($userProfile)) {

                return response()->json([
                    'message' => "laravel get show user profile where id false.",
                    'userProfile' => $userProfile
                ], 404);
            }

            return response()->json([
                'message' => "laravel get user show profile success.",
                'userProfile' => $userProfile
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel get show user profile function error",
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
            $validated = $request->validate([
                'profileID' => 'required|integer',
                'titleName' => 'required|string',
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'nickName' => 'required|string',
                'birthDay' => 'required|date',
            ]);

            $userProfile = UserProfile::findOrFail($request->profileID);

            // format birth day
            $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');

            $userProfile->updateOrCreate(
                ['id' => $userProfile->id],
                [
                    'title_name' => $validated['titleName'],
                    'first_name' => $validated['firstName'],
                    'last_name' => $validated['lastName'],
                    'nick_name' => $validated['nickName'],
                    'birth_day' => $birthDay,
                    'updated_at' => now()
                ]
            );

            if (empty($userProfile)) {
                return response()->json([
                    'message' => "laravel update user profile request or where id false",
                    'profileID' => $request->profileID,
                ], 404);
            }

            return response()->json([
                'message' => "laravel update user profile success",
                'profile' => $userProfile,
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "laravel update user profile function error",
                'error' => $error->getMessage()
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
