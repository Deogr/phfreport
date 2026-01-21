<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketItem extends Model
{
    protected $fillable = [
        'ticket_id',
        'code',
        'is_used', // boolean
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
