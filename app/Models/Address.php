<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function __construct(
        public string $street,
        public string $city,
        public string $country,
        public string $postal_code
    ) {
    }
}
