<?php

namespace App\Libs\Contracts\Modules;

/**
 * Discovery Module Abstract class.
 */
abstract class Discovery extends Module
{
    /**
     * Obtained models.
     *
     * @var Illuminate\Database\Eloquent\Model[]
     */
    protected $items = [];

    /**
     * Get the obtained models.
     *
     * @return Illuminate\Database\Eloquent\Model[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
