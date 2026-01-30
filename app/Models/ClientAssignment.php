<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'user_id',
        'therapist_id',
        'service_id',
        'ticket_item_id',
        'status',
        'appointment_time',
        'notes',
        'final_cost',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function ticketItem()
    {
        return $this->belongsTo(TicketItem::class);
    }
}
