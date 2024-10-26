<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tag::get()->all();
        return $tags;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tags' => ['required', 'array'],
            'tags.*.name' => ['required', 'string', 'max:255', "unique:tags"]
        ]);

        foreach ($request->tags as $tag){
            Tag::create([
                'name' => $tag['name'],
            ]);
        }
        $response = "the tags have been created successfully";
        return response()->json(['message' => $response], 200);
    }

    public function update(Request $request, $tag_id)
    {
        $validated = $request->validate([
            "name" => ['required', 'string', 'max:255', "unique:tags"]
        ]);
        $tag = Tag::find($tag_id);
        if ( !$tag){
            return response()->json(['message' => 'tag not found'], 404);
        }

        $tag->name = $request->name;
        $tag->save();

        $response = "the tag has been updated successfully";
        return response()->json(['message' => $response], 200);
    }

    public function delete($tag_id)
    {
        $tag = Tag::find($tag_id);
        if ( !$tag){
            return response()->json(['message' => 'tag not found'], 404);
        }
        $tag->delete();

        $response = "the tag has been deleted successfully";
        return response()->json(['message' => $response], 200);
    }
}
