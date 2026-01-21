<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'quantity',
        'price_per_ticket',
        'total_amount',
        'payment_method',
        'guest_name',
        'guest_phone',
        'status', // e.g., 'paid', 'used', 'cancelled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(TicketItem::class);
    }


}
