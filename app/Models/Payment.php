<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'payment_method', 'amount'];

    // Definisikan relasi dengan model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}