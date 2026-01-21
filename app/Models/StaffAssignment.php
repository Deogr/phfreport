<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'station_id',
        'assignment_date',
        'day_of_week',
        'start_time',
        'end_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
