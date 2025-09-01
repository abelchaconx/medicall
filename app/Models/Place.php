<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Place extends Model
{
    use HasFactory;

    protected $fillable = ['name','address_line','city','province','postal_code','phone','latitude','longitude'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_places')->withTimestamps();
    }

    public function doctorPlaces()
    {
        return $this->hasMany(DoctorPlace::class);
    }
}
