<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
    protected $fillable = ['employee_id', 'departement_id', 'name', 'address'];

    public function departement() {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
