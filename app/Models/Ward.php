<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
  protected $fillable = [
    'name',

  ];




  protected $table = 'wards';
  protected $primaryKey = 'code';
  public $incrementing = false;

  public function districts()
  {
    return $this->belongsTo(Province::class, 'district_code', 'code');
  }
}
