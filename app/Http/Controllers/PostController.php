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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with([
                'post_type',
                'post_images',
                'user_profiles',
                'user_profiles.users'
            ])
                ->where('status', 'active')
                ->get()
                ->map(function ($post) {
                    return $post;
                });


            if (empty($posts)) {
                return response()->json([
                    'message' => "api controller post function index require false",
                ], 404);
            }
            return response()->json([
                'message' => "api controller post function index require success.",
                'posts' => $posts,
            ], 200);
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

            $typeID = $validated['typeID'];
            if (!empty($validated['newType'])) {
                $postType = PostType::create([
                    'name' => $validated['newType'],
                    'created_at' => now()
                ]);
                $typeID = $postType->id;
            }

            if (empty($typeID)) {
                return response()->json([
                    'message' => 'Post type ID is missing.',
                ], 422);
            }

            $post = Post::create([
                'type_id' => $typeID,
                'profile_id' => $validated['profileID'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'refer' => $validated['refer'],
                'status' => 'active',
                'created_at' => now(),
            ]);

            if (empty($post)) {
                return response()->json([
                    'message' => "Post creation failed.",
                ], 500);
            }

            if ($request->hasFile('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'created_at' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Post created successfully.',
                'post' => $post,
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "An error occurred while creating the post.",
                'error' => $error->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $posts = Post::with([
                'post_type',
                'post_images',
                'user_profiles',
                'user_profiles.users'
            ])
                ->where('status', 'active')
                ->findOrFail($id);

            if (empty($posts)) {
                return response()->json([
                    'message' => "api controller post function show require false",
                ], 404);
            }
            return response()->json([
                'message' => "api controller post function show require success.",
                'posts' => $posts,
            ], 200);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "api controller post function show error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Function Update Post
     */
    public function update(Request $request, Post $post)
    {


        return response()->json([
            'message' => 'api controller test function update',
            'post' => $post
        ], 200);

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

            // $post = Post::findOrFail($post->);

            if (empty($post)) {
                return response()->json([
                    'message' => "Post creation failed.",
                ], 404);
            }

            $typeID = $validated['typeID'];
            if (!empty($validated['newType'])) {
                $postType = PostType::create([
                    'name' => $validated['newType'],
                    'created_at' => now()
                ]);
                $typeID = $postType->id;
            }

            if (empty($typeID)) {
                return response()->json([
                    'message' => 'Post type ID is missing.',
                ], 404);
            }

            $post = Post::create([
                'type_id' => $typeID,
                'profile_id' => $validated['profileID'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'refer' => $validated['refer'],
                'status' => 'active',
                'created_at' => now(),
            ]);



            if ($request->hasFile('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'created_at' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Post created successfully.',
                'post' => $post,
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "An error occurred while creating the post.",
                'error' => $error->getMessage(),
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
