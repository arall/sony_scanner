<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Severity Model class.
 */
class Severity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Findings relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
