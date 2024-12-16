<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDepartment extends Model
{

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');  // 'department_id' is the foreign key in this table
    }
}
