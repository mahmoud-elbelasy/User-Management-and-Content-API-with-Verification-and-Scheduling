<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Post_Tag;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $posts = $user->posts();
        $posts = Post::with('tags')->whereNull('deleted_at')->orderBy('pinned', 'desc')->get();
        return response()->json($posts);
    }

    public function showById(Request $request, $post_id)
    {
        $user = $request->user();
        $posts = $user->posts()->whereNull('deleted_at')->get();
        $post_ids = array();
        foreach($posts as $post){   
            array_push($post_ids, $post->id);
        }

        if (! in_array($post_id, $post_ids)){
            abort(404, 'Post not found');
        }

        $post = Post::where('id', $post_id)->with('tags')->get();

        return response()->json([
            'post' => $post
        ], 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif'],
            'pinned' => ['nullable', 'boolean'],
            'tags' => ['required', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('posts', $filename, 'public');

        $user = $request->user();
        $user_id = $user->id;

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => '/storage/' . $filePath,
            'pinned' => $request->pinned ?? false,
            'user_id' => $user_id
        ]);

        foreach($request->tags as $tag_id)
        {
            $tag = Tag::find($tag_id);
            if ($tag){
                Post_Tag::create([
                    'post_id' => $post->id,
                    'tag_id' => $tag->id
                ]);
            }else {
                abort(404, 'Tag not found');
            }
        }

        $response = "your post has been created successfuly";
        return response()->json(['message' => $response], 200);
    }

    public function update(Request $request, $post_id)
    {
        $user = $request->user();
        $posts = $user->posts()->whereNull('deleted_at')->get();
        $post_ids = array();
        foreach($posts as $post){   
            array_push($post_ids, $post->id);
        }

        if (! in_array($post_id, $post_ids)){
            abort(404, 'Post not found');
        }

        $post = Post::find($post_id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif'],
            'pinned' => ['nullable', 'boolean'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $post->fill($validated);
        

        if ($request->hasFile('image'))
        {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('posts', $filename, 'public');
            $post->image = '/storage/' . $filePath;
        }

        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    public function softDelete(Request $request, $post_id)
    {
        $user = $request->user();
        $posts = $user->posts()->whereNull('deleted_at')->get();
        $post_ids = array();
        foreach($posts as $post){   
            array_push($post_ids, $post->id);
        }

        if (! in_array($post_id, $post_ids)){
            abort(404, 'Post not found');
        }

        $post = Post::find($post_id);
        $post->deleted_at = now();
        $post->save();
        $response = "your post has been deleted successfully";
        return response()->json(['message' => $response], 200);
    }

    public function showDeleted(Request $request)
    {
        $user = $request->user();
        $posts = $user->posts();
        $posts = Post::with('tags')->whereNotNull('deleted_at')->orderBy('pinned', 'desc')->get();
        return response()->json($posts);
    }

    public function restore(Request $request, $post_id)
    {
        $user = $request->user();
        $posts = $user->posts()->whereNotNull('deleted_at')->get();
        $post_ids = array();
        foreach($posts as $post){   
            array_push($post_ids, $post->id);
        }

        if (! in_array($post_id, $post_ids)){
            abort(404, 'Post not found');
        }

        $post->deleted_at = null;
        $post->save();
        $response = "your post has been restored successfully";
        return response()->json(['message' => $response], 200);

    }




}
