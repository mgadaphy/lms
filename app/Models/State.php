<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';
    
    protected $fillable = [
        'name',
        'country_id'
    ];

    public $timestamps = false;

    /**
     * Get the country that owns the state.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the cities for the state.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the users for the state.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope to search states by name
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        }
        return $query;
    }

    /**
     * Scope to filter by country
     */
    public function scopeByCountry($query, $countryId)
    {
        if ($countryId) {
            return $query->where('country_id', $countryId);
        }
        return $query;
    }
}