<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'canonical',
        'publish',
        'user_id',
        'image'
    ];

    protected $table = 'Languages';

    public function languages()
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_language', 'language_id', 'post_catalogue_id')
            ->withPivot('name', 'canonical', 'meta-title', 'meta-keyword', 'meta-description', 'description', 'content')
            ->withTimestamps();
    }
}
