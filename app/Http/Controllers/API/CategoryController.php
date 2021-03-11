<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('created_user')->get();

        if (!empty($categories)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'categories' => $categories
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
            'name' => 'required|string|max:100',
            'details' => 'nullable',
            'status' => 'required',
        ]);

        $category = Category::create([
            'created_by' => Auth::user()->id,
            'name' => $request->input('name'),
            'details' => $request->input('details'),
            'status' => $request->input('status'),
        ]);

        if (!empty($category->id)) {
            return response()->json([
                'response_code' => 201,
                'message' => 'Success',
                'category' => $category
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
        $category = Category::with('created_user')->findOrFail($id);

        if (!empty($category)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'category' => $category
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
        $category = Category::with('created_user')->findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:100',
            'details' => 'nullable',
            'status' => 'required',
        ]);

        if (empty($category)) {
            return response()->json([
                'response_code' => 404,
                'message' => 'Not Found'
            ]);
        }

        $category->name = $request->input('name');
        $category->details = $request->input('details');
        $category->status = $request->input('status');
        $affected_row = $category->save();

        if (!empty($affected_row)) {
            return response()->json([
                'response_code' => 200,
                'message' => 'Success',
                'category' => $category
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
        $category = Category::with('created_user')->findOrFail($id);

        if (empty($category)) {
            return response()->json([
                'response_code' => 404,
                'message' => 'Not Found'
            ]);
        }

        if($category->delete()){
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
