<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    public function testFactory()
    {
        $employee1 = Employee::factory()->programmer()->make();
        $employee1->id = "1";
        $employee1->name = "Employee 1";
        $employee1->save();

        $this->assertNotNull(Employee::where("id", "1")->first());

        $employee2 = Employee::factory()->seniorProgrammer()->create([
            "id" => "2",
            "name" => "Employee 2"
        ]);

        $this->assertNotNull(Employee::where("id", "2")->first());
    }
}
