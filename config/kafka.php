<?php

return [
    'brokers' => env('KAFKA_BROKERS', 'kafka:9092'),
    'topics' => [
        'booking' => env('KAFKA_BOOKING_TOPIC', 'bookings'),
        'status' => env('KAFKA_STATUS_TOPIC', 'booking-status'),
    ],
];
