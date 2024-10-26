<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post_Tag extends Model
{
    public $table = 'post_tag';
    protected $hidden = [];
    protected $fillable = [ 'tag_id', 'post_id' ];
}
