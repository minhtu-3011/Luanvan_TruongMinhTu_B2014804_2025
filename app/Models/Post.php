<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [

        'image',
        'icon',
        'album',
        'publish',
        'follow',
        'order',
        'user_id'
    ];

    protected $table = 'posts';


    public function languages()
    {
        return $this->belongsToMany(Language::class, 'post_language', 'post_id', 'post_id')
            ->withPivot('name', 'canonical', 'meta-title', 'meta-keyword', 'meta-description', 'description', 'content')
            ->withTimestamps();
    }
    public function post_catalogues()
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_post', 'post_catalogue_id', 'post_id');
    }
}
