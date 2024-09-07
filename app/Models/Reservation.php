<?php

namespace App\Models;

use App\Enums\ReservationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'client_id','hotel_id','description','price','start_time','end_time',
        'start_date','end_date','days','people_number','status',
    ];

    protected $casts = [
        'status' => ReservationStatusEnum::class,
    ];

    public function client ():BelongsTo
    {
        return $this->belongsTo(User::class,'client_id');
    }

    public function hotel ():BelongsTo
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
