<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','license_number','bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalOffices()
    {
        return $this->belongsToMany(MedicalOffice::class, 'doctor_medicaloffices', 'doctor_id', 'medical_office_id')->withTimestamps();
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty')->withTimestamps();
    }

    public function doctorMedicalOffices()
    {
        return $this->hasMany(DoctorMedicaloffice::class);
    }
}
