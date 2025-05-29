<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reward;
use App\Models\RewardImage;
use App\Models\RewardType;

class RewardController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $rewards = Reward::with([
                'reward_status',
                'reward_images'
            ])
                ->get()
                ->map(function ($reward) {
                    return $reward;
                });

            if (empty($rewards)) {
                return response()->json([
                    'message' => 'api get reward false',
                    'rewards' => $rewards,
                ], 404);
            }

            return response()->json([
                'message' => 'api get reward success',
                'rewards' => $rewards,
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel get rewards function error',
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'typeID' => 'required|string',
                'newType' => 'nullable|string',
                'name' => 'required|string',
                'point' => 'required|numeric',
                'amount' => 'required|integer',
                'status' => 'required|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $typeID = $request->typeID;

            if (!empty($request->newType)) {

                $rewardType = RewardType::create([
                    'name' => $request->newType,
                    'created_at' => now()
                ]);

                $typeID = $rewardType->id;
            }

            if (empty($typeID)) {

                return response()->json([
                    'message' => 'laravel create reward type id false'
                ], 404);
            }

            $reward = Reward::create([
                'name' => $validated['name'],
                'point' => $validated['point'],
                'amount' => $validated['amount'],
                'status' => $validated['status']
            ]);

            if (empty($reward)) {

                return response()->json([
                    'message' => 'laravel create reward false',
                    'reward' => $reward
                ], 404);
            }

            if (!$request->hasFile('imageFile')) {
                $imageDataBase64 = null;
            } else {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);
            }

            RewardImage::create([
                'reward_id' => $reward->id,
                'image_data' => $imageDataBase64,
                'created_at' => now(),
            ]);


            return response()->json([
                'message' => 'reward created success',
                'reward' => $reward,
                'imageDataBase64' => $imageDataBase64
            ], 201);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'api function store create reward error',
                'error' => $error->getMessage()
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $rewards = Reward::with([
                'reward_status',
                'reward_images'
            ])->findOrFail($id);

            if (empty($rewards)) {
                return response()->json([
                    'message' => 'api get reward false',
                    'rewards' => $rewards,
                ], 404);
            }

            return response()->json([
                'message' => 'api get reward success',
                'rewards' => $rewards,
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel get rewards function error',
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {

            $validated = $request->validate([
                'typeID' => 'required|string',
                'newType' => 'nullable|string',
                'name' => 'required|string',
                'point' => 'required|numeric',
                'amount' => 'required|integer',
                'status' => 'required|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $typeID = $request->typeID;

            if (!empty($request->newType)) {

                $rewardType = RewardType::create([
                    'name' => $request->newType,
                    'created_at' => now()
                ]);

                $typeID = $rewardType->id;
            }

            if (empty($typeID)) {

                return response()->json([
                    'message' => 'laravel create reward type id false'
                ], 404);
            }

            $reward = Reward::findOrFail($request->rewardID);

            if (empty($reward)) {
                return response()->json([
                    'message' => 'api update reward response false',
                    'rewardID' => $request->rewardID
                ], 404);
            }

            $reward->updateOrCreate(
                ['id' => $reward->id],
                [
                    'name' => $request->name,
                    'point' => $request->point,
                    'amount' => $request->amount,
                    'status' => $request->status,
                    'updated_at' => now()
                ]
            );

            $rewardImage = RewardImage::where('reward_id', $reward->id)->first();

            if (empty($rewardImage)) {
                return response()->json([
                    'message' => 'laravel update reward image where id false',
                    'rewardID' => $reward->id,
                ], 404);
            }

            if (!$request->hasFile('imageFile')) {
                $imageDataBase64 = null;
            } else {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);
            }


            $rewardImage->updateOfCreate(
                ['id' => $rewardImage->id],
                [
                    'reward_id' => $reward->id,
                    'image_data' => $imageDataBase64,
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'message' => 'laravel update reward and image success',
                'reward' => $reward,
                'imageDataBase64' => $imageDataBase64
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel update reward and image function error',
                'error' => $error->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            DB::beginTransaction();
            $reward = Reward::with([
                'reward_images'
            ])->findOrFail($id);

            if (empty($reward)) {
                return response()->json([
                    'message' => 'laravel delete reward where id false',
                    'rewardID' => $id
                ], 404);
            }

            $reward->delete();
            DB::commit();
            return response()->json([
                'message' => 'laravel delete reward success',
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel delete reward function error',
                'error' => $error->getMessage()
            ]);
        }
    }
}
