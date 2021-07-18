<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Finding Model class.
 */
class Finding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vulnerability_id', 'title', 'details'];

    /**
     * Get all of the target models.
     *
     * @example A Website or a Host.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function target()
    {
        return $this->morphTo();
    }

    /**
     * Severity relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function severity()
    {
        return $this->belongsTo(Severity::class);
    }

    /**
     * Vulnerability relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vulnerability()
    {
        return $this->belongsTo(Vulnerability::class);
    }
}
