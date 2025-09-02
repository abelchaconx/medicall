<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name','label'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    // Backwards-compatible hasMany for legacy `role_id` usage
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Preferred many-to-many relationship
    public function usersMany()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }
}
