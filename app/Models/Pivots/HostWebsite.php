<?php

namespace App\Models\Pivots;

use App\Models\Port;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Host Website Pivot Model class.
 */
class HostWebsite extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'port_id',
    ];

    /**
     * Host relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * Website CWE relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Port relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function port()
    {
        return $this->belongsTo(Port::class);
    }
}
