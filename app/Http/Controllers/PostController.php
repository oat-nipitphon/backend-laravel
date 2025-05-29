<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\UserProfile;
use App\Models\UserWallet;
use App\Models\Post;
use App\Models\PostType;
use App\Models\PostImage;
use App\Models\PostDeletetion;
use App\Models\PostPop;

class PostController extends Controller
{


    // Function format date time
    private function dateTimeFormatTimeZone()
    {
        return Carbon::now('Asia/bangkok')->format('Y-m-d H:i:s');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with([
                'post_type',
                'post_image',
                'post_pops',
                'post_comments',
                'post_office_files',
                'post_videos',
                'user_profile',
                'user_profiles.users',
                'user_profiles.profile_image',
                'user_profiles.profile_pops',
                'user_profiles.profile_followers'
            ])
                ->get()
                ->map(function ($post) {
                    return $post;
                });


            if (!empty($posts)) {
                return response()->json([
                    'message' => "laravel get posts success.",
                    'posts' => $posts,
                ], 200);
            }

            return response()->json([
                'message' => "laravel get posts false",
            ], 404);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "api post controller function  error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Function Create Post
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'profileID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'nullable|string',
                'typeID' => 'nullable|integer',
                'newType' => 'nullable|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $typeID = $request->typeID;

            if (!empty($request->newType)) {

                $postType = PostType::create([
                    'name' => $request->newType,
                    'created_at' => now()
                ]);

                $typeID = $postType->id;
            }

            if (empty($typeID)) {

                return response()->json([
                    'message' => 'laravel create post type id false'
                ], 404);
            }


            $post = Post::create([
                'type_id' => $typeID,
                'profile_id' => $validated['profileID'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'refer' => $validated['refer'],
                'status' => "active",
                'created_at' => now(),
            ]);

            if (!empty($post)) {

                $postImage = new PostImage();
                $postImage['post_id'] = $post->id;

                if ($request->hasFile('imageFile')) {
                    $imageFile = $request->file('imageFile');
                    $imageData = file_get_contents($imageFile->getRealPath());
                    $imageDataBase64 = base64_encode($imageData);
                    $postImage['image_data'] = $imageDataBase64;
                } else {
                    $postImage['image_data'] = null;
                }

                $postImage['created_at'] = now();
                $postImage->save();


                $userProfile = UserProfile::findOrFail($validated['profileID']);

                $userWallet = UserWallet::firstOrCreate(
                    ['user_id' => $userProfile->user_id],
                    [
                        'point' => 0,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $userWallet->increment('point', 100);

                return response()->json([
                    'message' => 'laravel create post success',
                    'post' => $post,
                    'postImage' => $imageDataBase64,
                    'userProfile' => $userProfile,
                    'userWallet' => $userWallet
                ], 201);
            }

            return response()->json([
                'message' => "laravel create post false"
            ], 404);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "api post controller function store error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $post = Post::findOrFail($id);
            $post->with([
                'post_type',
                'post_image',
                'post_pops',
                'post_comments',
                'post_office_files',
                'post_videos',
                'user_profile',
                'user_profiles.users',
                'user_profiles.profile_image',
                'user_profiles.profile_pops',
                'user_profiles.profile_followers'
            ])
                ->get()
                ->map(function ($row) {
                    return $row;
                });


            if (!empty($post)) {
                return response()->json([
                    'message' => "laravel get show post success.",
                    'posts' => $post,
                ], 200);
            }

            return response()->json([
                'message' => "laravel get show post false",
            ], 404);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "laravel get show post function  error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Function Update Post
     */
    public function update(Request $request, string $postID)
    {
        try {

            $validated = $request->validate([
                'profileID' => 'required|integer',
                'postID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'nullable|string',
                'typeID' => 'nullable|integer',
                'newType' => 'nullable|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $typeID = $request->typeID;

            if (!empty($request->newType)) {

                $postType = PostType::create([
                    'name' => $request->newType,
                    'created_at' => now()
                ]);

                $typeID = $postType->id;
            }

            if (empty($typeID)) {

                return response()->json([
                    'message' => 'laravel create post type id false'
                ], 404);
            }

            $post = Post::findOrFail($request->postID);

            if (!empty($post)) {
                $post->update([
                    'type_id' => $typeID,
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'refer' => $validated['refer'],
                    'status' => "active",
                    'deletetion' => "false",
                    'updated_at' => now(),
                ]);

                $postImage = PostImage::where('post_id', $post->id)->first();

                if (!$request->hasFile('imageFile')) {
                    $imageDataBase64 = null;
                } else {
                    $imageFile = $request->file('imageFile');
                    $imageData = file_get_contents($imageFile->getRealPath());
                    $imageDataBase64 = base64_encode($imageData);
                }

                $postImage->forstOrCreate([
                    ['post_id' => $post->id],
                    ['image_data' => $imageDataBase64],
                    ['updated_at' => now()]
                ]);

                return response()->json([
                    'message' => 'laravel update post success',
                    'post' => $post,
                    'imageDataBase64' => $imageDataBase64
                ], 200);
            }


            return response()->json([
                'message' => 'laravel update post where post id response false',
                'postID' => $postID,
                'reqPostID' => $request->postID
            ], 404);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "api post controller function show error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();

            Post::with([
                'post_images',
                'post_pops',
                'post_comments',
                'post_office_files',
                'post_videos'
            ])->findOrFail($id);

            if (empty($post)) {
                return response()->json([
                    'message' => 'laravel delete post where post id false',
                    'postID' => $id
                ], 404);
            }

            $post->delete();

            DB::commit();

            return response()->json([
                'message' => "laravel delete post success",
            ], 200);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "laravel delete post function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
