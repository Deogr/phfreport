<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'station_id',
        'service_id',
        'user_count',
        'unit_price',
        'payment_method',
        'amount',
        'status',
        'shift_report_id',
        'subscription_id',
        'ticket_item_id'
    ];

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function shiftReport()
    {
        return $this->belongsTo(ShiftReport::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function ticketItem()
    {
        return $this->belongsTo(TicketItem::class);
    }
}
