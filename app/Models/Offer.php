<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name','hotel_id','description','price','start_time','end_time',
        'start_date','end_date','days','people_number','available',
    ];

    public function hotel ():BelongsTo
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
