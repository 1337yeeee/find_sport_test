<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * @param int $id
     * @return Booking|null
     */
    public function find(int $id): ?Booking
    {
        $booking = Booking::with('slots')->find($id);

        return $booking;
    }

    /**
     * Создает бронирование и временные слоты
     * 
     * @param array $data
     * @param int $userId
     * @return Booking
     * @throws ValidationException
     */
    public function create(array $data, int $userId): Booking
    {
        $slots = $data['slots'] ?? [];
        foreach ($slots as $slot) {
            $this->validateOverlaping($slot['start_time'], $slot['end_time']);
        }

        return DB::transaction(function () use ($slots, $userId) {
            $booking = Booking::create(['user_id' => $userId]);

            foreach ($slots as $slot) {
                $booking->slots()->create([
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }

            return $booking;
        });
    }

    /**
     * Добавляет временные слоты к бронированию
     * 
     * @param int $bookingId
     * @param array $data
     * @return Booking
     * @throws ValidationException
     */
    public function append(int $bookingId, array $data)
    {
        $slots = $data['slots'] ?? [];
        foreach ($slots as $slot) {
            $this->validateOverlaping($slot['start_time'], $slot['end_time']);
        }

        $booking = $this->find($bookingId);
        if ($booking === null) {
            throw ValidationException::withMessages([
                'booking_id' => ['Бронирование не найдено.'],
            ]);
        }

        return DB::transaction(function () use ($slots, $booking) {
            foreach ($slots as $slot) {
                $booking->slots()->create([
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }

            return $booking;
        });
    }

    /**
     * Проверяет, пересекаются ли временные метки с занятыми слотами
     * 
     * @param string $start start_time
     * @param string $end end_time
     * @return void
     * @throws ValidationException
     */
    private function validateOverlaping(string $start, string $end): void
    {
        $overlap = BookingSlot::where(function ($q) use ($start, $end) {
            $q->where('start_time', '<', $end)
            ->where('end_time', '>', $start);
        })->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'slots' => ['Один из временных слотов пересекается с уже существующим.'],
            ]);
        }
    }
}
