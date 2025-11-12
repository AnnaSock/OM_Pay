<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\Marchand;

class MarchandObserver
{
    /**
     * Handle the Marchand "created" event.
     */
    public function creating(Marchand $marchand): void
    {
        $marchand->role = Role::MARCHAND;
    }

    /**
     * Handle the Marchand "updated" event.
     */
    public function updated(Marchand $marchand): void
    {
        //
    }

    /**
     * Handle the Marchand "deleted" event.
     */
    public function deleted(Marchand $marchand): void
    {
        //
    }

    /**
     * Handle the Marchand "restored" event.
     */
    public function restored(Marchand $marchand): void
    {
        //
    }

    /**
     * Handle the Marchand "force deleted" event.
     */
    public function forceDeleted(Marchand $marchand): void
    {
        //
    }
}
