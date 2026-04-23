<?php

namespace App\Listeners;

use App\Events\EstateCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateEstateModFeature
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EstateCreated $event): void
    {
        //
    }
}
