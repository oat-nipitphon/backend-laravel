<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletCounter;
use App\Models\Reward;

class WalletCounterController extends Controller
{

    public function userConfirmSelectReward(Request $request)
    {
        try {

            $user_wallets = new UserWallet();
            $wallet_counters = new WalletCounter();
            $reward = new Reward();

            $request->validate([
                'userID' => 'required|integer',
                'userAmount' => 'required|integer',
                'counterItems' => 'required|json',
            ]);

            $counterItems = $request->input('counterItems');

            $user_wallets = UserWallet::where('user_id', $request->userID)->first();

            if (!$user_wallets) {

                return response()->json([
                    'message' => 'laravelapi user point request false',
                    'userID' => $request->userID,
                ], 404);
            }

            $user_wallets->update([
                'point' => $request->userAmount,
                'updated_at' => $this->dateTimeFormatTimeZone()
            ]);

            if (!$counterItems) {
                return response()->json([
                    'message' => 'laravelapi counter item request false',
                    'counterItems' => $counterItems = $request->input('counterItems'),
                ], 404);
            }

            $checkStatusCounter = false;
            foreach(json_decode($counterItems) as $item) {
                $data = [
                    'rewardID' => $item->rewardID,
                    'rewardName' => $item->rewardName,
                    'rewardPoint' => $item->rewardPoint,
                    'rewardAmount' => $item->rewardAmount,
                ];

                WalletCounter::create([
                    'user_id' => $request->userID,
                    'reward_id' => $data['rewardID'],
                    'point_status' => $data['rewardPoint'],
                    'created_at' => $this->dateTimeFormatTimeZone()
                ]);

                // จำนวนคงเหลือ ของรางวัล
                // $reward = Reward::findOrFail($data['rewardID']);
                // $

                $checkStatusCounter = true;
            }


            if ($checkStatusCounter !== true) {

                return response()->json([
                    'userWallets' => $user_wallets,
                    'counterItems' => $counterItems,
                    'message' => 'laravelapi create reward user point counter false',
                ], 404);

            }

            return response()->json([
                'userWallets' => $user_wallets,
                'counterItems' => $counterItems,
                'message' => 'laravelapi create reward user point counter success.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'api function userConfirmSelectReward error' . $e->getMessage()
            ]);
        }
    }

    public function getReportReward(Request $request, string $userID)
    {
        try {
            // ดึงข้อมูลเฉพาะผู้ใช้ พร้อม relation
            $user = User::with('user_wallet', 'user_wallet.wallet_counters', 'user_wallet.wallet_counters.reward')->findOrFail($userID);

            $userPointCounters = [
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'wallet' => $user->user_wallet->point,
                'walletCounters' => $user->user_wallet->wallet_counters?->map(function ($counter) {
                    return $counter ? [
                        'id' => $counter->id,
                        'walletID' => $counter->wallet_id,
                        'detail' => $counter->detail,
                        'created_at' => $counter->created_at,
                        'updated_at' => $counter->updated_at,
                        'rewards' => $counter->reward->map(function ($reward) {
                            return $reward ? [
                                'id' => $reward->id,
                                'point' => $reward->point,
                                'images' => $reward->rewardImage->map(function ($image) {
                                    return $image ? [
                                        'id' => $image->id,
                                        'image_data' => $image->image_data,
                                    ] : null;
                                }),
                            ] : null;
                        }),
                    ] : null;
                }),
            ];

            return response()->json([
                'message' => 'Laravel API get report reward success.',
                // 'userPointCounter' => $user,
                'userPointCounter' => $userPointCounters,
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Laravel API function get report reward error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function cancelReward (string $itemID) {
        try {

            $cancelReward = WalletCounter::findOrFail($itemID);

            if (empty($cancelReward)) {
                return response()->json([
                    'message' => 'laravelapi reward false',
                    'itemID' => $itemID
                ], 404);
            }

            $cancelReward->delete();


            return response()->json([
                'message' => 'laravelapi function cancel reward success'
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'function cancel reward error',
                'error' => $$error->getMessage(),
            ], 404);
        }
    }

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
