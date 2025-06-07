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
                'user_profiles.users.user_status'
            ])
                ->where('status', 'active')
                ->whereHas('user_profiles.users.user_status', function ($query) {
                    $query->whereIn('name', ['admin', 'user']);
                })
                ->get()
                ->map(function ($post) {
                    return $post ? [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content,
                        'refer' => $post->refer,
                        'created_at' => $post->created_at,
                        'updated_at' => $post->updated_at,

                        'post_type_name' => $post->post_type->name,

                        'post_images' => $post->post_images ? $post->post_images
                            ->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'image_data' => $image->image_data,
                                    'created_at' => $image->created_at,
                                    'updated_at' => $image->updated_at,
                                ];
                            }) : null,

                        'user_profile' => $post->user_profiles->id ?? null,
                        'title_name' => $post->user_profiles->title_name ??  '-',
                        'first_name' => $post->user_profiles->first_name ??  '-',
                        'last_name' => $post->user_profiles->last_name ??  '-',
                        'nick_name' => $post->user_profiles->nick_name ??  '-',
                        'birth_day' => $post->user_profiles->birth_day ?? null,
                        'created_at' => $post->user_profiles->created_at ?? null,
                        'updated_at' => $post->user_profiles->updated_at ?? null,


                        'id' => $post->user_profiles->users->id ?? null,
                        'username' => $post->user_profiles->users->username ?? '-',
                        'name' => $post->user_profiles->users->name ?? '-',
                        'email' => $post->user_profiles->users->email ?? '-',
                        'created_at' => $post->user_profiles->users->created_at ?? null,
                        'updated_at' => $post->user_profiles->users->updated_at ?? null,

                        'user_status_id' => $post->user_profiles->users->user_status->id ?? null,
                        'user_status_name' => $post->user_profiles->users->user_status->name ?? '-',

                    ] : null;
                });


            if (!$posts) {
                return response()->json([
                    'message' => "api controller post function index require false",
                ], 500);
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
            $request->validate([
                'profile_id' => 'required|integer',
                'type_id' => 'nullable|integer',
                'new_type' => 'nullable|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $type_id = $request->type_id;
            if (!empty($request->new_type)) {
                $post_type = PostType::create([
                    'name' => $request->new_type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $type_id = $post_type->id;
            }

            if (empty($type_id)) {
                return response()->json([
                    'message' => 'Post type ID is missing.',
                ], 422);
            }

            $post = Post::create([
                'type_id' => $type_id,
                'profile_id' => $request->profile_id,
                'title' => $request->title,
                'content' => $request->content,
                'refer' => $request->refer,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$post) {
                return response()->json([
                    'message' => "Post creation failed.",
                ], 500);
            }

            if ($request->hasFile('image_file')) {
                $image_file = $request->file('image_file');
                $image_data = file_get_contents($image_file->getRealPath());
                $image_data_base64 = base64_encode($image_data);

                $post_image = PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $image_data_base64,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            if (!$post_image) {
                return response()->json([
                    'message' => 'post image require false'
                ], 500);
            }

            return response()->json([
                'message' => 'Post created successfully.'
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
    public function update(Request $request, string $id)
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

            $post = Post::findOrFail($id);

            if (empty($post)) {
                return response()->json([
                    'message' => "api controller function update where post id false.",
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
                    'message' => 'api controller function update type ID is missing.',
                ], 404);
            }

            $post->updateOrCreate(
                ['id' => $post->id],
                [
                    'type_id' => $typeID,
                    'profile_id' => $validated['profileID'],
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'refer' => $validated['refer'],
                    'status' => 'active',
                    'updated_at' => now(),
                ]
            );

            if ($request->hasFile('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);
                $post->post_images->update([
                    'image_data' => $imageDataBase64,
                    'updated_at' => now()
                ], 201);
            }

            return response()->json([
                'message' => 'api controller function update successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "api controller function update post error",
                'error' => $e->getMessage(),
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

            $post = Post::findOrFail($id);

            if (empty($post)) {
                return response()->json([
                    'message' => 'api controller function destroy where post id false.',
                ], 404);
            }

            $post->delete();

            DB::commit();

            return response()->json([
                'message' => "api controller function destroy post successfully.",
            ], 200);

        } catch (\Exception $error) {

            return response()->json([
                'message' => "api controller function post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
