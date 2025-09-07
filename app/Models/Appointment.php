<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['patient_id','doctor_medicaloffice_id','start_datetime','end_datetime','status','notes'];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    'deleted_at' => 'datetime',
    ];

    protected $appends = [];

    // add new fillable attributes
    public function getFillable()
    {
        return array_merge($this->fillable, ['schedule_id','consultation_type','consultation_notes']);
    }

    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorMedicalOffice()
    {
        return $this->belongsTo(DoctorMedicaloffice::class, 'doctor_medicaloffice_id');
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
