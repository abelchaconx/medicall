<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id','amount','currency','method','provider','transaction_id','status','response_payload'];

    protected $casts = [
        'response_payload' => 'array',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
