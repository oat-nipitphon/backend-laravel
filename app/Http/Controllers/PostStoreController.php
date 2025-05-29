<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PostStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $profileID)
    {
        try {

            $posts = PostStore::with(['posts'])
                ->where('status', 'true')
                ->whereHas('posts', function ($query) use ($profileID) {
                    $query->whereIn('profile_id', $profileID);
                })
                ->get();

            if (empty($posts)) {
                return response()->json([
                    'message' => 'laravel get posts store where profile id false',
                    'profileID' => $profileID
                ], 404);
            }

            return response()->json([
                'message' => 'laravel get posts store success',
                'posts' => $posts
            ], 200);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "Laravel function recover get post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $postID)
    {
        try {

            $post = Post::findOrFail($postID);

            if (!empty($post)) {

                $post->updateOrCreate([
                    ['post_id' => $post->id],
                    [
                        'status' => 'store'
                    ]
                ]);

                $postStore = PostStore::updateOrCreate([
                    ['post_id' => $post->id],
                    [
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);

                return response()->json([
                    'message' => "laravel new post store success",
                    'post' => $post->status,
                    'postStore' => $postStore
                ], 201);
            }

            return response()->json([
                'message' => "laravel new post where id response false",
                'postID' => $postID,
            ], 404);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel function postStore error " . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PostStore $postStore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Recover Store Post Status Active
     */
    public function updateStorePost(Request $request, PostStore $postStore, string $postID)
    {
        try {

            $post = Post::where('id', $postID)->first();

            if (empty($post)) {
                return response()->json([
                    'message' => "laravel update post where id false",
                    'postID' => $postID,
                ], 404);
            }

            $post->update([
                'status' => 'active'
            ]);

            return response()->json([
                'message' => "laravel update post success",
                'post' => $post
            ], 201);
        } catch (\Exception $error) {

            return response()->json([
                'message' => "laravel update post function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function updateStorePosts(Request $request)
    {
        try {

            $ids = $request->ids;

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'message' => 'laravel update store posts response array false',
                    'reqPostIDs' => $request->ids
                ], 404);
            }


            $posts = Post::whereIn('id', $ids)->get();

            foreach ($posts as $post) {
                $post->update([
                    'status' => 'active',
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'message' => 'laravel update posts selected success',
                'recoverPosts' => $posts
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel update posts selected function error',
                'error' => $error->getMessage()
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostStore $postStore, string $id)
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

    public function destroyPosts(Request $request)
    {
        try {

            DB::beginTransaction();

            Post::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'message' => "laravel delete posts success",
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'message' => "laravel delete posts function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
