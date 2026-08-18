<?php

namespace App\Observers;

use App\Models\Bug;

class BugObserver
{
    /**
     * Handle the Bug "created" event.
     */
    public function created(Bug $bug): void
    {
        //
    }

    /**
     * Handle the Bug "updated" event.
     */
    public function updated(Bug $bug): void
    {
        //
    }

    /**
     * Handle the Bug "deleted" event.
     */
    public function deleted(Bug $bug): void
    {
        //
    }

    /**
     * Handle the Bug "restored" event.
     */
    public function restored(Bug $bug): void
    {
        //
    }

    /**
     * Handle the Bug "force deleted" event.
     */
    public function forceDeleted(Bug $bug): void
    {
        //
    }
}
