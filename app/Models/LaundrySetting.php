<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\MilikCabang;
use Illuminate\Database\Eloquent\Model;

class LaundrySetting extends Model
{
    use HasFactory, MilikCabang;

    protected $fillable = [
      'cabang_id','user_id','target_day','target_month','target_year'
    ];
}
