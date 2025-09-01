<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorPlace extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id','place_id','notes'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
