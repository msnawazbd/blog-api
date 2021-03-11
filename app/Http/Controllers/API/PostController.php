<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('created_user', 'category')->get();

        if (!empty($posts)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'posts' => $posts
            ]);
        }
        return response()->json([
            'response_code' => 404,
            'message' => 'Not Found'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'category_id' => 'required',
            'details' => 'nullable',
            'status' => 'required',
        ]);

        $post = Post::create([
            'created_by' => Auth::user()->id,
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'status' => $request->input('status'),
        ]);

        if (!empty($post->id)) {
            return response()->json([
                'response_code' => 201,
                'message' => 'Success',
                'post' => $post
            ]);
        }
        return response()->json([
            'response_code' => 500,
            'message' => 'Failed'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with('created_user', 'category')->findOrFail($id);

        if (!empty($post)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'post' => $post
            ]);
        }
        return response()->json([
            'response_code' => 404,
            'message' => 'Not Found'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::with('created_user', 'category')->findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:100',
            'category_id' => 'required',
            'details' => 'nullable',
            'status' => 'required',
        ]);

        if (empty($post)) {
            return response()->json([
                'response_code' => 404,
                'message' => 'Not Found'
            ]);
        }

        $post->title = $request->input('title');
        $post->category_id = $request->input('category_id');
        $post->details = $request->input('details');
        $post->status = $request->input('status');
        $affected_row = $post->save();

        if (!empty($affected_row)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'post' => $post
            ]);
        }
        return response()->json([
            'response_code' => 500,
            'message' => 'Failed'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::with('created_user', 'category')->findOrFail($id);

        if (empty($post)) {
            return response()->json([
                'response_code' => 404,
                'message' => 'Not Found'
            ]);
        }

        if($post->delete()){
            return response()->json([
                'response_code' => 204,
                'message' => 'No Content'
            ]);
        }
        return response()->json([
            'response_code' => 500,
            'message' => 'Failed'
        ]);
    }
}
