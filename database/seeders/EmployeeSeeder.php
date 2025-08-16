<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::insert([
            [
                'employee_id' => 'EMP-1',
                'departement_id' => 1,
                'name' => 'Teguh Saputra',
                'address' => 'Jl. Merpati No. 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],          
        ]);
    }
}
