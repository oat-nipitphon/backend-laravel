<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfilePopController;
use App\Http\Controllers\UserProfileFollowersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WalletCounterController;

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminRewardController;
use App\Http\Controllers\AdminUserProfileController;
use App\Http\Controllers\AdminWalletController;

use App\Models\UserStatus;
use App\Models\PostType;
use App\Models\RewardStatus;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/get_user_status', function () {
    $userStatus = UserStatus::all();
    return response()->json($userStatus, 200);
});

Route::post('/register/check_email', [AuthController::class, 'registerCheckEmail']);
Route::post('/register/check_username', [AuthController::class, 'registerCheckUsername']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('/')->group(function () {

    // ********** User Profiles *************
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/user_profiles', UserProfileController::class);
    Route::apiResource('/user_profile_pops', UserProfilePopController::class);
    Route::post('/pop_like/{postUserID}/{authUserID}', [UserProfilePopController::class, 'popLikeProfile']);
    Route::apiResource('/user_profile_followers', UserProfileFollowersController::class);
    Route::post('/followers/{postUserID}/{authUserID}', [UserProfileFollowersController::class, 'followersProfile']);


    // ********** Posts ***************
    Route::get('/get_post_types', function () {
        $postTypes = PostType::all();
        return response()->json($postTypes, 200);
    });
    Route::apiResource('/posts', PostController::class);
    Route::post('/posts/store/{postID}', [PostController::class, 'postStore']);
    Route::post('/posts/update', [PostController::class, 'update']);
    Route::post('/posts/recover/{postID}', [PostController::class, 'recoverPost']);
    Route::post('/posts/report_recover/{userID}', [PostController::class, 'recoverGetPost']);
    Route::post('/posts/recoverSelected', [PostController::class, 'recoverSelected']);
    Route::post('/posts/deleteSelected', [PostController::class, 'deleteSelected']);
    Route::post('/{userID}/{postID}/{popStatusLike}', [PostController::class, 'postPopLike']);
    Route::post('/{userID}/{postID}/{popStatusDisLike}', [PostController::class, 'postPopDisLike']);


    // ********** Rewards *************
    Route::get('/get_reward_type', function () {
        $rewardStatus = RewardStatus::all();
        return response()->json($rewardStatus, 200);
    });
    Route::apiResource('rewards', RewardController::class);

    // ********** Wallets *************
    Route::apiResource('wellets', WalletController::class);
    Route::apiResource('wallet_counters', WalletCounterController::class);
    Route::prefix('/cartItems')->group(function () {
        Route::post('/userConfirmSelectReward', [WalletCounterController::class, 'userConfirmSelectReward']);
        Route::get('/getReportReward/{userID}', [WalletCounterController::class, 'getReportReward']);
        Route::post('/cancel_reward/{itemID}', [WalletCounterController::class, 'cancelReward']);
    });


    // Route Admin Manager Blog
    Route::prefix('/manager')->group(function () {
        Route::apiResource('/user_profiles', AdminUserProfileController::class);
        Route::apiResource('/posts', AdminPostController::class);
        Route::post('/blockOrUnBlock/{postID}/{blockStatus}', [AdminPostController::class, 'blockOrUnBlockPost']);
        Route::prefix('/reward')->group(function () {
            Route::apiResource('/manager', AdminRewardController::class);
            Route::post('/updateStatusReward/{rewardID}', [AdminRewardController::class, 'updateStatusReward']);
        });
        Route::apiResource('/wallets', AdminWalletController::class);
    })->name('manager');
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    try {

        $user_req = $request->user();
        $users = User::with([
            'user_status',
            'user_wallet',
            'user_wallet.wallet_counters',
            'user_profile',
            'user_profile.profile_image'

        ])->findOrFail($user_req->id);

        $token = $users->createToken($users->username)->plainTextToken;

        $users =  [
            'id' => $users->id,
            'name' => $users->name,
            'email' => $users->email,
            'username' => $users->username,
            'status_id' => $users->status_id,
            'created_at' => $users->created_at,
            'updated_at' => $users->updated_at,

            'userStatus' => $users->user_status ? [
                'id' => $users->user_status->id,
                'name' => $users->user_status->name,
                'code' => $users->user_status->code,
            ] : null,


            'userProfile' => $users->user_profile ? [
                'id' => $users->user_profile->id,
                'titleName' => $users->user_profile->title_name,
                'firstName' => $users->user_profile->first_name,
                'lastName' => $users->user_profile->last_name,
                'nickName' => $users->user_profile->nick_name,
                'birthDay' => $users->user_profile->birth_day,
            ] : null,


            'profileImage' => $users->user_profile->profile_image ?
                $users->user_profile->profile_image->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'profile_id' => $image->profile_id,
                        'imageData' => $image->image_data,
                    ];
                }) : null,


            'wallet' => $users->user_wallet ? [
                'id' => $users->user_wallet->id,
                'userID' => $users->user_wallet->user_id,
                'point' => $users->user_wallet->point,
                'status' => $users->user_wallet->status,
            ] : null,


            'walletCounters' => $users->user_wallet->wallet_counters ?
                $users->user_wallet->wallet_counters->map(function ($counter) {
                    return [
                        'id' => $counter->id,
                        'walletID' => $counter->wallet_id,
                        'rewardID' => $counter->reward_id,
                        'point' => $counter->point,
                        'status' => $counter->reward_id,
                        'detail' => $counter->detail,
                        'createdAt' => $counter->created_at,
                        'updatedAt' => $counter->updated_at,
                    ];
                }) : null,
        ];

        if (empty($users)) {
            return response()->json([
                'message' => 'get user false',
                'users' => '',
                'token' => ''
            ], 404);
        }

        return response()->json([
            'message' => 'get user successfllry.',
            'users' => $users,
            'token' => $token
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'get user function error',
            'error' => $e->getMessage()
        ], 500);
    }
});
