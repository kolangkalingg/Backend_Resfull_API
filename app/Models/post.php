<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'content',
        'image',
    ];

public function image() : Attribute
{

    return Attribute::make(
        get: fn($image) => url('/storage/posts/' . $image),
    );

}

}
