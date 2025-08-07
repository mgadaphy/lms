<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';
    
    protected $fillable = [
        'name',
        'code',
        'phone_code'
    ];

    public $timestamps = false;

    /**
     * Get the states for the country.
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the users for the country.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope to search countries by name
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        }
        return $query;
    }
}