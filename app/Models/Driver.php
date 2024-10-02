<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
    ];

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
