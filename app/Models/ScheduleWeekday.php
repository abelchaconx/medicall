<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleWeekday extends Model
{
    use HasFactory;

    protected $table = 'schedule_weekdays';
    protected $fillable = ['schedule_id','weekday'];
    public $timestamps = true;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
