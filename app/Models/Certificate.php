<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Certificate Model class.
 */
class Certificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'serial'];

    /**
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    /**
     * Findings relation.
     *
     * @example a port without an installation (as the product was not obtained) can be brute-forced.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function findings()
    {
        return $this->morphMany(Finding::class, 'target');
    }
}
