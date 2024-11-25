<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);  // A teacher belongs to a user
    }

    public function department()
    {
        return $this->belongsTo(Department::class);  // A teacher belongs to a department
    }
}
