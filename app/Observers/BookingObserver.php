<?php

namespace App\Observers;

use App\Models\Bookings;
use App\Services\KafkaProducer;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use App\Models\Topics;

class BookingObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Bookings "created" event.
     */
    public function created(Bookings $booking): void
    {
        $data = $booking->toArray();
        app(KafkaProducer::class)->send($data);

        Topics::query()->create([
            'booking_id' => $booking->id,
            'sent_message' => json_encode($data)
        ]);
    }
}
