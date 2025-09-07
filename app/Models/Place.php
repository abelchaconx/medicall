<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name','address_line','city','province','otros','phone','latitude','longitude'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_medicaloffices')->withTimestamps();
    }

    public function doctorPlaces()
    {
    return $this->hasMany(DoctorMedicaloffice::class);
    }
}
