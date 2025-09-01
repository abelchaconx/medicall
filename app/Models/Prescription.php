<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['appointment_id','file_path','mime_type','file_size','observations','uploaded_by'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
