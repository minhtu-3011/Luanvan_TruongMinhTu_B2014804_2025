<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\QueryScopes;

class Language extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;
    protected $fillable = [
        'name',
        'canonical',
        'publish',
        'user_id',
        'image',
        'current'
    ];

    protected $table = 'languages';

    public function languages()
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_language', 'language_id', 'post_catalogue_id')
            ->withPivot('name', 'canonical', 'meta-title', 'meta-keyword', 'meta-description', 'description', 'content')
            ->withTimestamps();
    }
}
