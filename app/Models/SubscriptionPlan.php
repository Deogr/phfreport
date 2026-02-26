<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'description',
        'status',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
