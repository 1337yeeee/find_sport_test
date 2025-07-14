<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'start_time',
        'end_time',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
