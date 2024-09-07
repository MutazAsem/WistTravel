<?php

namespace App\Models;

use App\Enums\CountryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name','country','image'
    ];

    protected $casts = [
        'country' => CountryEnum::class,
    ];


}
