<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class System extends Model
{
    use HasFactory;
    protected $table = 'systems';


    public function languages()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
