<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCatalogueLanguage extends Model
{
    use HasFactory;

    protected $table = 'product_catalogue_language';

    public function product_catalogues()
    {
        return $this->belongsTo(ProductCatalogue::class, 'product_catalogue_id', 'id')->where('language_id', '=', 5);
    }
}
