<?php

namespace App\Observers;

use App\Models\Bookings;
use App\Services\KafkaProducer;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class BookingObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Bookings "created" event.
     */
    public function created(Bookings $booking): void
    {
        app(KafkaProducer::class)->send($booking->toArray());
    }
}
