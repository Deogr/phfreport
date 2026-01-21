<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    protected $fillable = [
        'user_id',
        'station_id',
        'start_time',
        'end_time',
        'total_cash',
        'total_momo',
        'total_revenue',
        'status',
        'manager_id',
        'approved_at',
        'rejection_reason',
        'total_tickets',
        'total_subscriptions'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
