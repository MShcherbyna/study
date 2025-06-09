<?php

namespace App\Services;

use App\Models\Bookings;
use App\Models\Topics;
use Illuminate\Support\Facades\Redis;

class TopicDataHandler
{
    const TTL = 86400;

    public Topics $topic;

    public function handle(array $data): bool
    {
        if (empty($this->topic)) {
            \Log::error('Booking message not found.');

            return false;
        }

        try {
            $affected = Bookings::where('id', $data['id'])->update([
                'booking_status' => $data['status'],
                'validation_response' => $data['message'] ?? 'Success.'
            ]);

            if ($affected === 0) {
                $message = "Booking not found for id {$data['id']}";
                \Log::error($message);
                $this->topic->update([
                    'status' => Topics::ERROR,
                    'error_message' => $message,
                    'received_message' => $data
                ]);

                return false;
            }

            $this->topic->update([
                'status' => Topics::SUCCESS,
                'received_message' => $data
            ]);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            \Log::error($e->getMessage());
            $this->topic->update([
                'status' => Topics::ERROR,
                'error_message' => $message,
                'received_message' => $data
            ]);

            return false;
        }

        return true;
    }

    public function setTopic(string $id): ?TopicDataHandler
    {
        $topic = Topics::query()->where('booking_id', $id)->first();
        if (!$topic) {
            \Log::error("Topic not found {$id}");

            return null;
        }

        $this->topic = $topic;

        return $this;
    }

    public function getRetryNumber(): int
    {
        $key = "booking_status_retry:{$this->topic->booking_id}";

        return (int) (Redis::get($key) ?? 0);
    }

    public function incrementRetryNumber(): TopicDataHandler
    {
        $key = "booking_status_retry:{$this->topic->booking_id}";
        $retries = Redis::incr($key);
        if ($retries === 1) {
            Redis::expire($key, self::TTL);
        }

        return $this;
    }

    public function close(int $status, ?string $message = null): TopicDataHandler
    {
        $this->topic->update([
            'status' => $status,
            'error_message' => $message,
        ]);

        return $this;
    }
}
