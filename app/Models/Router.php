<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Router extends Model
{
    use HasFactory;
    protected $table = 'routers';

    protected $fillable = [
        'canonical',
        'module_id',
        'controllers',
        'language_id',   // thêm dòng này
    ];
}
