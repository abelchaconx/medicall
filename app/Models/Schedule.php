<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['doctor_medicaloffice_id','weekday','weekdays','start_time','end_time','duration_minutes','valid_from','valid_to','batch_id'];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        // keep time fields as string/time in DB; Blade will render them directly
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function doctorMedicalOffice()
    {
        return $this->belongsTo(DoctorMedicaloffice::class, 'doctor_medicaloffice_id');
    }

    public function exceptions()
    {
        return $this->hasMany(ScheduleException::class, 'schedule_id');
    }

    public function weekdaysRelation()
    {
        return $this->hasMany(ScheduleWeekday::class, 'schedule_id');
    }

    /**
     * Return array of weekday integers for this schedule, prefer relational weekdays if available
     */
    public function getWeekdaysArrayAttribute()
    {
        try {
            $rels = $this->weekdaysRelation()->pluck('weekday')->filter()->values()->all();
            if (! empty($rels)) {
                return is_array($rels) ? $rels : [];
            }
            
            if (! empty($this->weekdays)) {
                $parts = preg_split('/\s*,\s*/', trim($this->weekdays));
                if ($parts === false) return [];
                
                $days = array_filter(array_map('intval', $parts));
                return array_values(array_unique($days));
            }
            
            if ($this->weekday !== null) {
                return [(int)$this->weekday];
            }
            
            return [];
        } catch (\Exception $e) {
            \Log::error('Error in getWeekdaysArrayAttribute: ' . $e->getMessage());
            return [];
        }
    }
}
