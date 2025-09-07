<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorMedicaloffice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'doctor_medicaloffices';
    protected $fillable = ['doctor_id','medical_office_id','notes'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medicalOffice()
    {
        return $this->belongsTo(MedicalOffice::class, 'medical_office_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'doctor_medicaloffice_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_medicaloffice_id');
    }
}
