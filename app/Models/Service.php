<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'price', 'description', 'status'];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class);
    }
}
