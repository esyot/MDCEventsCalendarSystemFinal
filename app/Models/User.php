<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserPermission;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    public function user_permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }


    // A user has one teacher record (teacher table links users to departments)
    public function teacher()
    {
        return $this->hasOne(Teacher::class);  // One user can have one teacher record
    }

    // Full name as an accessor
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function venueCoordinators()
    {
        return $this->hasMany(VenueCoordinator::class);
    }

}
