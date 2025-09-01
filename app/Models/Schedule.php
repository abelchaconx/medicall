<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['doctor_place_id','weekday','start_time','end_time','duration_minutes'];

    public function doctorPlace()
    {
        return $this->belongsTo(DoctorPlace::class);
    }
}
