<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image'];

    public function carts()
    {
        return $this->hasMany(Cart::class); // Pastikan ini ada
    }
    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : asset('storage/default.png'); // Gambar default jika tidak ada
    }
}