<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use Illuminate\Http\Request;
use App\Models\Post;
use File;
use Str;

class PostController extends Controller
{
	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index() {
		// All Post
		$post = Post::all();

		// Return Json Response
		return response()->json([
			'post' => $post
		], 200);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create() {
		//
	}

	/**
	* Store a newly created resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function store(PostStoreRequest $request) {
		try {

			$imageName = Str::random(32).".".$request->image->getClientOriginalExtension();

			// Create Post
			Post::create([
				'name' => $request->name,
				'image' => $imageName,
				'description' => $request->description
			]);

			// Save Image in folder
			$request->image->move('image/', $imageName);

			// Return Json Response
			return response()->json([
				'message' => "Post successfully created."
			], 200);

		}catch(\Exception $e) {

			// Return Json Response
			return response()->json([
				'message' => "Something went really wrong!"
			], 500);

		}
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function show($id) {
		// Post Detail
		$post = Post::find($id);

		if (!$post) {
			return response()->json([
				'message' => 'Post Not Found'
			], 404);
		}

		// Return Json Response
		return response()->json([
			'post' => $post
		], 200);
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id) {
		//
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function update(PostStoreRequest $request, $id) {

		try {
			// Find Post
			$post = Post::find($id);

			if (!$post) {

				return response()->json([
					'message' => 'Post Not Found'
				], 404);

			}

			$post->name = $request->name;
			$post->description = $request->description;

			if ($request->image) {

				// File Path
				$filePath = 'image/'. $post->image;

				if (File::exists($filePath))
					// Old Delete File
				File::delete($filePath);

				// Image name
				$imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
				$post->image = $imageName;

				// Image move in public folder
				$request->image->move('image/', $imageName);
			}

			// Update Post
			$post->save();

			// Return Json Response
			return response()->json([
				'message' => "Post successfully updated."
			], 200);

		}catch(\Exception $e) {

			// Return Json Response
			return response()->json([
				'message' => "Something went really wrong!"
			], 500);

		}
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function destroy($id) {
		// Post Detail
		$post = Post::find($id);
		if (!$post) {
			return response()->json([
				'message' => 'Post Not Found.'
			], 404);
		}

		// File Path
		$filePath = 'image/'. $post->image;

		if (File::exists($filePath))
			// Delete File
		File::delete($filePath);

		// Delete Post
		$post->delete();

		// Return Json Response
		return response()->json([
			'message' => "Post successfully deleted."
		], 200);
	}
}