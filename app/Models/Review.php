<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = "reviews";
    protected $primaryKey = "id";
    protected $keyType = "bigInt";
    public $incrementing = false;
    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "category_id", "id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }
}
