<?php

namespace Modules\BookingModule\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\BookingModule\Entities\PostBid;
use Modules\BookingModule\Http\Controllers\PostBidController;
use Illuminate\Support\Facades\Log;
use Modules\BookingModule\Http\Traits\BookingTrait;

class BookingService
{
    use BookingTrait;
    public function processBooking($customerUserId, array $request)
    {
        return DB::transaction(function () use ($customerUserId, $request) {

            // -----------------------------
            // 1. Minimum Booking Check
            // -----------------------------
            if (!isset($request['post_id'])) {
                $minimumBookingAmount = Cache::remember(
                    'min_booking_amount',
                    300,
                    fn() => (float)(business_config('min_booking_amount', 'booking_setup'))?->live_values
                );

                if ($minimumBookingAmount > 0) {
                    $totalBookingAmount = cart_total($customerUserId) + getServiceFee();
                    if ($totalBookingAmount < $minimumBookingAmount) {
                        return ['flag' => 'failed', 'message' => 'Minimum booking amount not reached'];
                    }
                }
            }

            // -----------------------------
            // 2. Bidding Flow
            // -----------------------------
            if (isset($request['post_id'])) {
                $postBid = PostBid::where('post_id', $request['post_id'])
                    ->where('provider_id', $request['provider_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$postBid || $postBid->status !== 'pending') {
                    return ['flag' => 'failed', 'message' => 'Bid not available or already accepted'];
                }

                $post = DB::table('posts')
                    ->where('id', $postBid->post_id)
                    ->select('service_id', 'category_id', 'booking_schedule', 'service_address_id')
                    ->first();

                $serviceTax = DB::table('services')
                    ->where('id', $post->service_id)
                    ->value('tax');

                $price = $postBid->offered_price;
                $tax = $serviceTax ? round(($price * $serviceTax) / 100, 2) : 0;

                // Wallet check and deduction
                if ($request['payment_method'] === 'wallet_payment') {
                    $walletBalance = DB::table('users')->where('id', $customerUserId)->lockForUpdate()->value('wallet_balance');
                    if ($walletBalance < ($price + $tax)) {
                        return ['flag' => 'failed', 'message' => 'Insufficient wallet balance'];
                    }

                    DB::table('users')->where('id', $customerUserId)->update([
                        'wallet_balance' => $walletBalance - ($price + $tax)
                    ]);
                }

                // Place booking for bidding
                $bookingId = app()->make(\Modules\BookingModule\Services\CoreBookingService::class)
                    ->placeBookingRequestForBidding($customerUserId, $request, $price, $tax);

                // Accept the bid
                $postBid->update(['status' => 'accepted']);
                PostBidController::acceptPostBidOffer($postBid->id, $bookingId);

                return ['flag' => 'success', 'booking_id' => $bookingId];
            }

            // -----------------------------
            // 3. Normal / Repeat Booking
            // -----------------------------
            $transactionId = match ($request['payment_method'] ?? '') {
                'wallet_payment'  => 'wallet_payment',
                'offline_payment' => 'offline-payment',
                default           => 'cash-payment',
            };

            if (($request['service_type'] ?? '') === 'repeat') {
                $bookingId = app()->make(\Modules\BookingModule\Services\CoreBookingService::class)
                    ->placeRepeatBookingRequest($customerUserId, $request, $transactionId);
            } else {
                $bookingId = $this->placeBookingRequest($customerUserId, $request, $transactionId);
            }

            return ['flag' => 'success', 'booking_id' => $bookingId];
        });
    }
}