<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $hidden= [];
    protected $fillable = ['title', 'body', 'image', 'pinned', 'user_id'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
