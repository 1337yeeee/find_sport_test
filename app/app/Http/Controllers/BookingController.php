<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppendBookingSlotsRequest;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}
    
    public function show(int $id)
    {
        $booking = $this->bookingService->find($id);
        if ($booking === null) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        return response()->json($booking);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->create(
            $request->validated(),
            auth()->id()
        );

        return response()->json($booking->load('slots'), 201);
    }

    public function append(int $bookingId, StoreBookingRequest $request)
    {
        $booking = $this->bookingService->append(
            $bookingId,
            $request->validated()
        );

        return response()->json($booking->load('slots'), 200);
    }
}
