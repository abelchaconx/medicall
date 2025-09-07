<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalOffice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medical_offices';

    protected $fillable = ['name','address_line','city','province','otros','phone','latitude','longitude'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_medicaloffices', 'medical_office_id', 'doctor_id')->withTimestamps();
    }

    public function doctorMedicalOffices()
    {
        return $this->hasMany(DoctorMedicaloffice::class, 'medical_office_id');
    }
}
