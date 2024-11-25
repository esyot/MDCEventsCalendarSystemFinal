<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    // A department has many teachers
    public function teachers()
    {
        return $this->hasMany(Teacher::class);  // A department can have many teachers
    }
}
