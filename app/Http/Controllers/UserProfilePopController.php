<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserProfileFollowers;
use App\Models\UserProfilePop;
use Illuminate\Http\Request;

class UserProfilePopController extends Controller
{


    public function popLikeProfile(Request $request, string $profileID, string $profileIDPop)
    {
        try {

            $pops = UserProfilePop::where('profile_id', $profileID)
                ->whereIn('profile_id_pop', $profileIDPop)->first();

            $checkStatusPop = '';

            if ($pops) {

                if ($pops->status === 'like') {
                    $pops->delete();
                    $checkStatusPop = 'like';
                } else {
                    $pops->update([
                        'status' => 'like',
                        'updated_at' => now(),
                    ]);
                    $checkStatusPop = 'like';
                }
            } else {

                UserProfilePop::create([
                    'profile_id' => $profileID,
                    'profile_id_pop' => $profileIDPop,
                    'status' => 'like',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $checkStatusPop = 'like';
            }

            if (empty($checkStatusPop)) {
                return response()->json([
                    'message' => 'laravel update status user profile pop like false.',
                    'checkStatusPop' => $checkStatusPop
                ], 404);
            }

            return response()->json([
                'message' => 'laravel update status user profile pop like success.',
                'checkStatusPop' => $checkStatusPop
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel popLikeProfile function error",
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
