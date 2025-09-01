<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','license_number','bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'doctor_places')->withTimestamps();
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty')->withTimestamps();
    }

    public function doctorPlaces()
    {
        return $this->hasMany(DoctorPlace::class);
    }
}
