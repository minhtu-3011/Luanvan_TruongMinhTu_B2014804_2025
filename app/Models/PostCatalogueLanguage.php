<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostCatalogueLanguage extends Model
{
    use HasFactory;

    protected $table = 'post_catalogue_language';

    public function post_catalogues()
    {
        return $this->belongsTo(PostCatalogue::class, 'post_catalogue_id', 'id')->where('language_id', '=', 5);
    }
}
