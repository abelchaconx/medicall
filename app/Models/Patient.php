<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','birthdate','phone','notes','gender'];

    /**
     * Append convenient accessors to the model's array form.
     * This exposes $patient->name which proxies to the related user's name.
     *
     * @var array<int,string>
     */
    protected $appends = ['name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the patient's display name (proxied to the related user).
     */
    public function getNameAttribute()
    {
        return $this->user?->name;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
