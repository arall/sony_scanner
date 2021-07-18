<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Website Model class.
 */
class Website extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url', 'auth'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'auth' => 'array',
    ];

    /**
     * URL cleanup.
     *
     * @param string $value
     */
    public function setUrlAttribute($value)
    {
        $parts = parse_url($value);

        // Default scheme
        if (!isset($parts['scheme'])) {
            $url = 'http';
        } else {
            $url = $parts['scheme'];
        }

        $url .= '://' . $parts['host'];

        // Ignore default ports
        if (isset($parts['port']) && !in_array($parts['port'], [80, 443])) {
            $url .= ':' . $parts['port'];
        }

        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }

        $this->attributes['url'] = $url;
    }

    /**
     * Fix for using setUrlAttribute method in firstOrCreate method.
     *
     * @param array $attributes
     * @return Website
     */
    public static function firstOrCreate(array $attributes)
    {
        $attributes = (new self)->fill($attributes)->attributesToArray();

        if (!is_null($instance = self::where($attributes)->first())) {
            return $instance;
        }

        $instance = (new self)->newInstance($attributes);

        $instance->save();

        return $instance;
    }

    /**
     * Certificate relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Hosts relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hosts()
    {
        return $this->belongsToMany(Host::class)->withPivot('port_id');
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
