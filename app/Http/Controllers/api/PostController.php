<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::latest()->get();

        return response(
            [
                'success' => true,
                'message' => 'List semua post',
                'data' => $post,
            ],
            200,
        );
    }

    public function store(Request $request)
    {
        //validasi form
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'content' => 'required',
            ],
            [
                'title.required' => 'Masukkan title post',
                'content.required' => 'Masukkan content post',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Isi form yang kosong',
                    'data' => $validator->errors(),
                ],
                401,
            );
        } else {
            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);

            if ($post) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Post berhasil disimpan!',
                    ],
                    200,
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Post berhasil dihapus!',
                    ],
                    401,
                );
            }
        }
    }
}