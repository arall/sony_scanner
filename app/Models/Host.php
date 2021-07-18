<?php

namespace App\Models;

use App\Models\Pivots\HostWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Host Model class.
 */
class Host extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'ip'];

    /**
     * Ports relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    /**
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites()
    {
        return $this->belongsToMany(Websites::class)->withPivot('port_id')->using(HostWebsite::class);;
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
