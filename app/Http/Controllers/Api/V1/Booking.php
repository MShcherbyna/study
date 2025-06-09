<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Bookings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Booking extends Controller
{
    public function store(BookingRequest $request): Response
    {
        $booking = Bookings::query()->create($request->validated());

        return response($booking->id, 200);
    }

    public function show(string $bookingId): JsonResponse
    {
        return response()->json(Bookings::query()->find($bookingId));
    }

    public function index(): JsonResponse
    {
        return response()->json(Bookings::all());
    }
}
