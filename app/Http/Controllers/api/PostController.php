<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Tambahkan ini di bagian atas jika belum ada


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();

        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'title'     => 'required',
            'content'   => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        $post = Post::find($id);

        return new PostResource(true, 'Detail Data Post', $post);
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($post->image) {
                $oldImagePath = 'public/posts/' . $post->image;
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                    Log::info("Deleted old image: " . $oldImagePath); // Log deletion
                } else {
                    Log::warning("Image to delete not found: " . $oldImagePath); // Log if image not found
                }
            }

            $image = $request->file('image');
            $newImageName = $image->hashName();
            $image->storeAs('public/posts/', $newImageName);

            // Update post with new image
            $post->update([
                'image'     => $newImageName,
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {
            // Update post without changing the image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah', $post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        Storage::delete('public/posts/' . $post->image);

        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus', $post);

    }
}