<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'spn_cities';
    
    protected $fillable = [
        'name',
        'state_id'
    ];

    public $timestamps = false;

    /**
     * Get the state that owns the city.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country through the state.
     */
    public function country()
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

    /**
     * Get the users for the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope to search cities by name
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        }
        return $query;
    }

    /**
     * Scope to filter by state
     */
    public function scopeByState($query, $stateId)
    {
        if ($stateId) {
            return $query->where('state_id', $stateId);
        }
        return $query;
    }
}