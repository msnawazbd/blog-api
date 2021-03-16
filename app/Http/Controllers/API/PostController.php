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
        $create_post = Post::create([
            'created_by' => Auth::user()->id,
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'status' => $request->input('status'),
        ]);

        if ($request->input('base64_encoded_file')) {
            // convert to array after and before base64,
            $base64_encode_data = explode('base64,', $request->input('base64_encoded_file'));
            // get last index which is base64_encode value
            $base64_decode_data = base64_decode($base64_encode_data[1]);
            // get file original extension
            $file_original_name = explode('.', $request->input('file_original_name'));
            $file_original_extension = end($file_original_name);
            // create unique file name
            $file_unique_name = time() . '.' . $file_original_extension;
            // create upload path
            $upload_path = public_path() . $request->input('file_upload_path') . $file_unique_name;
            // returns the number of bytes that were written to the file, or false on failure.
            $success = file_put_contents($upload_path, $base64_decode_data);

            if($success){
                $post = Post::findOrFail($create_post->id);
                $post->photo = $file_unique_name;
                $post->save();
            }
        }

        if (!empty($create_post->id)) {
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

        if ($post->delete()) {
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
