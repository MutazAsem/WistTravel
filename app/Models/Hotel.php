<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name','description','image','services','advantages','stars',
        'city_id','address_link','address_description'
    ];

    public function city ():BelongsTo
    {
        return $this->belongsTo(City::class,'city_id');
    }

}
