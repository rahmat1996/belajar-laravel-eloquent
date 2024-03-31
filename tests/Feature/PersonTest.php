<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $person = new Person();
        $person->first_name = "Rahmat";
        $person->last_name = "Saja";
        $person->save();

        $this->assertEquals("RAHMAT Saja", $person->fullName);

        $person->fullName = "Budi Santoso";
        $person->save();
        assertEquals("BUDI", $person->first_name);
        assertEquals("Santoso", $person->last_name);
    }

    public function testAttributeCasting()
    {
        $person = new Person();
        $person->first_name = "Rahmat";
        $person->last_name = "Saja";
        $person->save();

        $this->assertNotNull($person->created_at);
        $this->assertNotNull($person->updated_at);
        $this->assertInstanceOf(Carbon::class, $person->created_at);
        $this->assertInstanceOf(Carbon::class, $person->updated_at);
    }

    public function testCustomCasts()
    {
        $person = new Person();
        $person->first_name = "Rahmat";
        $person->last_name = "Saja";
        $person->address = new Address("Jalan Martadinata", "Jakarta", "Indonesia", "23456");
        $person->save();

        $this->assertNotNull($person->created_at);
        $this->assertNotNull($person->updated_at);
        $this->assertInstanceOf(Carbon::class, $person->created_at);
        $this->assertInstanceOf(Carbon::class, $person->updated_at);
        $this->assertEquals("Jalan Martadinata", $person->address->street);
        $this->assertEquals("Jakarta", $person->address->city);
        $this->assertEquals("Indonesia", $person->address->country);
        $this->assertEquals("23456", $person->address->postal_code);
    }
}
