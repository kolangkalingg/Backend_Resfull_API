<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostRecousrce;
use App\Http\Resources\PostResource;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function index(){
        $posts = Post::latest()->paginate(5);

        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'title'     => 'required',
            'content'   => 'required'
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts/', $image->hashName());

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

        return new PostResource(true, 'Detail Data Post',$post);
}


    //method update
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
            'image'   => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $post = Post::findOrFail($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($post->image && Storage::exists('public/posts/' . $post->image)) {
                Storage::delete('public/posts/' . $post->image);
            }

            // Upload gambar baru
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Update post dengan gambar baru
            $post->update([
                'image'   => $image->hashName(),
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        } else {
            // Update post tanpa mengubah gambar
            $post->update([
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah', $post);
    }


    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Hapus gambar jika ada
        if ($post->image && Storage::exists('public/posts/' . $post->image)) {
            Storage::delete('public/posts/' . $post->image);
        }

        // Hapus post dari database
        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus', null);
    }


}