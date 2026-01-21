<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = ['name', 'location', 'status'];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function shiftReports()
    {
        return $this->hasMany(ShiftReport::class);
    }
}
