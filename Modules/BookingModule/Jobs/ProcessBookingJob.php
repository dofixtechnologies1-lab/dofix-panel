<?php

namespace Modules\BookingModule\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\BookingModule\Services\BookingService;
use Illuminate\Support\Facades\Log;

class ProcessBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customerUserId;
    public array $request;
    public ?int $newUserId;

    public $tries = 3;
    public $timeout = 120;

    public function __construct($customerUserId, array $request, ?int $newUserId = null)
    {
        $this->customerUserId = $customerUserId;
        $this->request = $request;
        $this->newUserId = $newUserId;
    }

    public function handle(BookingService $bookingService)
    {
        try {
            $response = $bookingService->processBooking($this->customerUserId, $this->request);

            if (($response['flag'] ?? null) !== 'success') {
                Log::warning('Booking job did not complete successfully', [
                    'customerUserId' => $this->customerUserId,
                    'request' => $this->request,
                    'response' => $response
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('ProcessBookingJob failed', [
                'customerUserId' => $this->customerUserId,
                'request' => $this->request,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Rethrow to trigger retry if needed
            throw $e;
        }
    }
}