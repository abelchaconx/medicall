<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id','doctor_place_id','start_datetime','end_datetime','status','notes'];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorPlace()
    {
        return $this->belongsTo(DoctorPlace::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
