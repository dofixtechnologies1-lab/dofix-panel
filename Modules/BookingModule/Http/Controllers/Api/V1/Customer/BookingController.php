<?php

namespace Modules\BookingModule\Http\Controllers\Api\V1\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\BidModule\Entities\PostBid;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\BookingOfflinePayment;
use Modules\BookingModule\Entities\BookingPartialPayment;
use Modules\BookingModule\Entities\BookingRepeat;
use Modules\PaymentModule\Entities\PaymentRequest;
use Modules\UserManagement\Entities\User;
use Modules\BookingModule\Entities\Booking;
use Modules\PaymentModule\Entities\OfflinePayment;
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\CustomerModule\Traits\CustomerAddressTrait;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\BidModule\Http\Controllers\APi\V1\Customer\PostBidController;
use Modules\BookingModule\Entities\ServiceEnquiry;
use Modules\ServiceManagement\Entities\Service;
use Log;
use Modules\CartModule\Entities\Cart;
use Modules\CartModule\Http\Controllers\Api\V1\Customer\CartController;
use Modules\ServiceManagement\Entities\Variation;
use Modules\UserManagement\Entities\Guest;
use Modules\CartModule\Traits\AddedToCartTrait;
use Illuminate\Support\Facades\Config;
use Modules\BookingModule\Entities\Addon;
use Modules\BookingModule\Entities\BookingDetail;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;
use Modules\ServiceManagement\Entities\FavoriteService;
use Modules\ServiceManagement\Entities\RecentView;
use Modules\TransactionModule\Entities\Transaction;
use Modules\CategoryManagement\Entities\CategoryExtra;

class BookingController extends Controller
{
    use BookingTrait, CustomerAddressTrait, AddedToCartTrait;

    private Booking $booking;
    private BookingStatusHistory $bookingStatusHistory;
    private Cart $cart;
    protected OfflinePayment $offlinePayment;
    private BookingRepeat $bookingRepeat;
    private bool $isCustomerLoggedIn;
    private mixed $customerUserId;
    private Guest $guest;
    private BusinessSettings $business_setting;
    private Service $service;
    private FavoriteService $favoriteService;
    private RecentView $recentView;

    private bool $is_customer_logged_in;

    private Transaction $transaction;


    public function __construct(Transaction $transaction, RecentView $recentView, FavoriteService $favoriteService, Service $service, Cart $cart, Guest $guest, Booking $booking, BookingStatusHistory $bookingStatusHistory, Request $request, OfflinePayment $offlinePayment, BookingRepeat $bookingRepeat, BusinessSettings $business_setting)
    {
        $this->booking = $booking;
        $this->bookingStatusHistory = $bookingStatusHistory;
        $this->offlinePayment = $offlinePayment;
        $this->bookingRepeat = $bookingRepeat;
        $this->cart = $cart;
        $this->guest = $guest;
        $this->business_setting = $business_setting;
        $this->isCustomerLoggedIn = (bool)auth('api')->user();
        $this->customerUserId = $this->isCustomerLoggedIn ? auth('api')->user()->id : $request['guest_id'];
        $this->service = $service;
        $this->favoriteService = $favoriteService;
        $this->recentView = $recentView;
        $this->transaction = $transaction;
    }

    # Service Enquiry

    public function serviceEnquiryStore(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|max:300',
            'mobile_number' => 'required',
            'email' => 'nullable|email',
            'service_id' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'address' => 'required',
            'variations' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $customerId = $request->user()->id;
            // $created_enquiry = ServiceEnquiry::create([
            //     'name' => $request->name,
            //     'mobile_number' => $request->mobile_number,
            //     'email' => $request->email,
            //     'service_id' => $request->service_id,
            //     'date' => $request->date,
            //     'time' => $request->time,
            //     'address' => $request->address,
            //     'message' => $request->message,
            //     'lat' => $request->lat,
            //     'lng' => $request->lng,
            //     'zoneID' => $request->zone_id,
            //     'status' => 0,
            //     'user_id'=>$customerId,
            // ]);

            $request['service_schedule'] = $request->date . ' ' . $request->time;
            $request['service_address'] = json_encode(
                [
                    'id' => null,
                    'address_type' => 'service',
                    'address_label' => 'home',
                    'contact_person_name' => $request->name,
                    'contact_person_number' => $request->mobile_number,
                    'address' => $request->address,
                    'lat' => $request->lat,
                    'lon' => $request->lng,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'country' => $request->country,
                    'zone_id' => $request->zone_id,
                    '_method' => null,
                    'street' => '',
                    'house' => '',
                    'floor' => '',
                    'available_service_count' => null
                ]

            );

            // First Add to cart
            $serviceData = Service::where('id', $request->service_id)->first();

            $request['category_id'] = $serviceData->category_id;
            $request['sub_category_id'] = $serviceData->sub_category_id;

            foreach ($request->variations as $variant_key) {
                $request['variant_key'] = $variant_key;
                $request['quantity'] = 1;

                $this->addToCart($request);
            }


            // Store in Booking Table
            return $this->placeRequest($request);


            // return response()->json([
            //     'success' => true,
            //     'message' => 'Booking request submitted successfully',
            //     'data' => $created_enquiry
            // ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //  This is add to cart this is only for temporary purpose to store in booking enquiry

    public function addToCart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'uuid',
            'service_id' => 'required|uuid',
            'category_id' => 'required|uuid',
            'sub_category_id' => 'required|uuid',
            'variant_key' => 'required',
            'quantity' => 'required|numeric|min:1|max:1000',
            'guest_id' => $this->isCustomerLoggedIn ? 'nullable' : 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $customerUserId = $this->customerUserId;

        $this->addedToCartUpdate($customerUserId, $request['service_id'], !$this->isCustomerLoggedIn);

        if ($request['provider_id']) {
            $nextBookingEligibility = nextBookingEligibility($request['provider_id']);
            if (!$nextBookingEligibility) {
                return response()->json(response_formatter(BOOKING_ELIGIBILITY_FOR_BOOKING), 400);
            }
        }

        $variation = Variation::
        where(['zone_id' => Config::get('zone_id'), 'service_id' => $request['service_id']])
            ->where(['variant_key' => $request['variant_key']])
            ->first();

        if (isset($variation)) {
            $service = Service::find($request['service_id']);

            $checkCart = Cart::where([
                'service_id' => $request['service_id'],
                'variant_key' => $request['variant_key'],
                'customer_id' => $customerUserId])->first();
            $cart = $checkCart ?? $this->cart;
            $quantity = $request['quantity'];

            $basicDiscount = basic_discount_calculation($service, $variation->price * $quantity);
            $campaignDiscount = campaign_discount_calculation($service, $variation->price * $quantity);
            $subtotal = round($variation->price * $quantity, 2);

            $applicableDiscount = ($campaignDiscount >= $basicDiscount) ? $campaignDiscount : $basicDiscount;

            $tax = round((($variation->price * $quantity - $applicableDiscount) * $service['tax']) / 100, 2);

            //between normal discount & campaign discount, greater one will be calculated
            $basicDiscount = $basicDiscount > $campaignDiscount ? $basicDiscount : 0;
            $campaignDiscount = $campaignDiscount >= $basicDiscount ? $campaignDiscount : 0;


            $total_cost = round($subtotal - $basicDiscount - $campaignDiscount + $tax, 2);


            $data_values = $this->business_setting->where('key_name', 'partial_payment')->first();

            $partial_amount = ($total_cost * ($data_values->live_values / 100));


            $cart->provider_id = $request['provider_id'];
            $cart->customer_id = $customerUserId;
            $cart->service_id = $request['service_id'];
            $cart->category_id = $request['category_id'];
            $cart->sub_category_id = $request['sub_category_id'];
            $cart->variant_key = $request['variant_key'];
            $cart->quantity = $quantity;
            $cart->service_cost = $variation->price;
            $cart->discount_amount = $basicDiscount;
            $cart->campaign_discount = $campaignDiscount;
            $cart->coupon_discount = 0;
            $cart->coupon_code = null;
            $cart->is_guest = !$this->isCustomerLoggedIn;
            $cart->tax_amount = round($tax, 2);
            $cart->total_cost = round($subtotal - $basicDiscount - $campaignDiscount + $tax, 2);
            $cart->partial_amount = $partial_amount;
            $cart->save();

            if (!$this->isCustomerLoggedIn) {
                $guest = $this->guest;
                $guest->ip_address = $request->ip();
                $guest->guest_id = $request->guest_id;
                $guest->save();
            }

            return response()->json(response_formatter(DEFAULT_STORE_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function placeRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:' . implode(',', array_column(PAYMENT_METHODS, 'key')),
            'zone_id' => 'required|uuid',
            'service_schedule' => 'required',
            'service_address_id' => is_null($request['service_address']) ? 'required' : 'nullable',
            'service_preference' => 'required',
            'post_id' => 'nullable|uuid',
            'provider_id' => 'nullable|uuid',

            'guest_id' => $this->isCustomerLoggedIn ? 'nullable' : 'required|uuid',
            //'offline_payment_id' => 'required_if:payment_method,offline_payment',
            //'customer_information' => 'required_if:payment_method,offline_payment',
            'service_address' => is_null($request['service_address_id']) ? [
                'required',
                'json',
                function ($attribute, $value, $fail) {
                    $decoded = json_decode($value, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $fail($attribute . ' must be a valid JSON string.');
                        return;
                    }

                    if (is_null($decoded['lat']) || $decoded['lat'] == '') $fail($attribute . ' must contain "lat" properties.');
                    if (is_null($decoded['lon']) || $decoded['lon'] == '') $fail($attribute . ' must contain "lon" properties.');
                    if (is_null($decoded['address']) || $decoded['address'] == '') $fail($attribute . ' must contain "address" properties.');
                    if (is_null($decoded['contact_person_name']) || $decoded['contact_person_name'] == '') $fail($attribute . ' must contain "contact_person_name" properties.');
                    if (is_null($decoded['contact_person_number']) || $decoded['contact_person_number'] == '') $fail($attribute . ' must contain "contact_person_number" properties.');
                    if (is_null($decoded['address_label']) || $decoded['address_label'] == '') $fail($attribute . ' must contain "address_label" properties.');
                },
            ] : '',

            'is_partial' => 'nullable|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }


        $newUserInfo = null;
        if ($request->has('new_user_info') && !empty($request->get('new_user_info')) && !$this->isCustomerLoggedIn) {
            $newUserInfo = json_decode($request['new_user_info'], true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($newUserInfo)) {
                return response()->json(response_formatter(DEFAULT_400, null, 'Invalid new_user_info format'), 400);
            }

            $newUserValidator = Validator::make($newUserInfo, [
                'first_name' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'password' => 'required|min:8',
            ]);

            if ($newUserValidator->fails()) {
                return response()->json(response_formatter(DEFAULT_400, null, error_processor($newUserValidator)), 400);
            }
        }

        $customerUserId = $this->customerUserId;

        if (is_null($request['service_address_id'])) {
            $request['service_address_id'] = $this->add_address(json_decode($request['service_address']), null, !$this->isCustomerLoggedIn);
        }

        $minimumBookingAmount = (float)(business_config('min_booking_amount', 'booking_setup'))?->live_values;
        $totalBookingAmount = cart_total($customerUserId) + getServiceFee();

        if (!isset($request['post_id']) && $minimumBookingAmount > 0 && $totalBookingAmount < $minimumBookingAmount) {
            return response()->json(response_formatter(MINIMUM_BOOKING_AMOUNT_200), 200);
        }

        if ($request['payment_method'] == 'wallet_payment') {
            if (!isset($request['post_id'])) {
                $response = $this->placeBookingRequest(userId: $customerUserId, request: $request, transactionId: 'wallet_payment', newUserInfo: $newUserInfo);
            } else {
                $postBid = PostBid::with(['post'])
                    ->where('post_id', $request['post_id'])
                    ->where('provider_id', $request['provider_id'])
                    ->first();

                $data = [
                    'payment_method' => $request['payment_method'],
                    'zone_id' => $request['zone_id'],
                    'service_tax' => $postBid?->post?->service?->tax,
                    'provider_id' => $postBid->provider_id,
                    'price' => $postBid->offered_price,
                    'service_schedule' => !is_null($request['booking_schedule']) ? $request['booking_schedule'] : $postBid->post->booking_schedule,
                    'service_id' => $postBid->post->service_id,
                    'category_id' => $postBid->post->category_id,
                    'sub_category_id' => $postBid->post->category_id,
                    'service_address_id' => !is_null($request['service_address_id']) ? $request['service_address_id'] : $postBid->post->service_address_id,
                    'is_partial' => $request['is_partial']
                ];

                $user = User::find($customerUserId);
                $tax = !is_null($data['service_tax']) ? round((($data['price'] * $data['service_tax']) / 100) * 1, 2) : 0;
                if (isset($user) && $user->wallet_balance < ($postBid->offered_price + $tax)) {
                    return response()->json(response_formatter(INSUFFICIENT_WALLET_BALANCE_400), 400);
                }

                $response = $this->placeBookingRequestForBidding($customerUserId, $request, 'wallet_payment', $data);

                if ($response['flag'] == 'success') {
                    PostBidController::acceptPostBidOffer($postBid->id, $response['booking_id']);
                }
            }

        } elseif ($request['payment_method'] == 'offline_payment') {
            if (!isset($request['post_id'])) {
                $response = $this->placeBookingRequest($customerUserId, $request, 'offline-payment', newUserInfo: $newUserInfo, isGuest: !$this->isCustomerLoggedIn);

            } else {
                $postBid = PostBid::with(['post'])
                    ->where('post_id', $request['post_id'])
                    ->where('provider_id', $request['provider_id'])
                    ->first();

                $data = [
                    'payment_method' => $request['payment_method'],
                    'zone_id' => $request['zone_id'],
                    'service_tax' => $postBid?->post?->service?->tax,
                    'provider_id' => $postBid->provider_id,
                    'price' => $postBid->offered_price,
                    'service_schedule' => !is_null($request['booking_schedule']) ? $request['booking_schedule'] : $postBid->post->booking_schedule,
                    'service_id' => $postBid->post->service_id,
                    'category_id' => $postBid->post->category_id,
                    'sub_category_id' => $postBid->post->category_id,
                    'service_address_id' => !is_null($request['service_address_id']) ? $request['service_address_id'] : $postBid->post->service_address_id,
                    'is_partial' => $request['is_partial']
                ];

                $response = $this->placeBookingRequestForBidding($customerUserId, $request, 'offline_payment', $data);

                if ($response['flag'] == 'success') {
                    PostBidController::acceptPostBidOffer($postBid->id, $response['booking_id']);
                }
            }
        } else {
            if ($request['service_type'] == 'repeat') {
                $response = $this->placeRepeatBookingRequest($customerUserId, $request, 'cash-payment', newUserInfo: $newUserInfo, isGuest: !$this->isCustomerLoggedIn);
            } else {

                $response = $this->placeBookingRequest($customerUserId, $request, 'cash-payment', newUserInfo: $newUserInfo, isGuest: !$this->isCustomerLoggedIn);

            }
        }

        return response()->json($response, 200);

        if ($response['flag'] == 'success') {
            return response()->json(response_formatter(BOOKING_PLACE_SUCCESS_200, $response), 200);
        } else {
            return response()->json(response_formatter(BOOKING_PLACE_FAIL_200), 200);
        }
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'booking_status' => 'required|in:all,' . implode(',', array_column(BOOKING_STATUSES, 'key')),
            'service_type' => 'required|in:all,regular,repeat',
            'string' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $bookings = $this->booking
            ->with(['customer', 'repeat', 'subCategory', 'category'])
            ->where(['customer_id' => $request->user()->id])
            ->search(base64_decode($request['string']), ['readable_id'])
            ->when($request['booking_status'] != 'all', function ($query) use ($request) {
                return $query->ofBookingStatus($request['booking_status']);
            })
            ->when($request['service_type'] != 'all', function ($query) use ($request) {
                return $query->ofRepeatBookingStatus($request['service_type'] === 'repeat' ? 1 : ($request['service_type'] === 'regular' ? 0 : null));
            })
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])
            ->withPath('');

        foreach ($bookings as $booking) {
            if ($booking->repeat->isNotEmpty()) {
                $sortedRepeats = $booking->repeat->sortBy(function ($repeat) {
                    $parts = explode('-', $repeat->readable_id);
                    $suffix = end($parts);
                    return $this->readableIdToNumber($suffix);
                });
                $booking->repeats = $sortedRepeats->values()->toArray();
            }
            unset($booking->repeat);
        }

        return response()->json(response_formatter(DEFAULT_200, $bookings), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $booking = $this->booking
            ->where('customer_id', $request->user()->id)
            ->where('id', $id)
            ->with([
                'detail.service',
                'schedule_histories.user',
                'status_histories.user',
                'service_address',
                'customer',
                'provider',
                'category.extras',
                'subCategory:id,name',
                'serviceman.user',
                'booking_partial_payments',
                'repeat.scheduleHistories',
                'repeat.repeatHistories'
            ])
            ->first();

        if (isset($booking)) {
            $offlinePayment = $booking->booking_offline_payments?->first();

            if ($offlinePayment) {
                $booking->booking_offline_payment_method = $offlinePayment->method_name;
                $booking->booking_offline_payment = collect($offlinePayment->customer_information)->map(function ($value, $key) {
                    return ["key" => $key, "value" => $value];
                })->values()->all();

                $booking->offline_payment_id = $offlinePayment->offline_payment_id ?? null;
                $booking->offline_payment_status = $offlinePayment->payment_status ?? null;
                $booking->offline_payment_denied_note = $offlinePayment->denied_note ?? null;
            }

            unset($booking->booking_offline_payments);

            if (isset($booking->provider)) {
                $booking->provider->chatEligibility = chatEligibility($booking->provider_id);
            }

            if ($booking->repeat->isNotEmpty()) {
                $repeatHistoryCollection = $booking->repeat->flatMap(function ($repeat) {
                    return $repeat->repeatHistories->map(function ($history) {
                        $history->log_details = json_decode($history->log_details);
                        return $history;
                    });
                });

                $booking['repeatHistory'] = $repeatHistoryCollection->toArray();
                $sortedRepeats = $booking->repeat->sortBy(function ($repeat) {
                    $parts = explode('-', $repeat->readable_id);
                    $suffix = end($parts);
                    return $this->readableIdToNumber($suffix);
                });
                $booking['repeats'] = $sortedRepeats->values()->toArray();

                $nextService = collect($booking['repeats'])->firstWhere('booking_status', 'accepted');
                if (!$nextService) {
                    $nextService = collect($booking['repeats'])->firstWhere('booking_status', 'pending');
                }

                $booking['nextService'] = $nextService;
                $booking['time'] = max(collect($booking['repeats'])->pluck('service_schedule')->flatten()->toArray());
                $booking['startDate'] = min(collect($booking['repeats'])->pluck('service_schedule')->flatten()->toArray());
                $booking['endDate'] = max(collect($booking['repeats'])->pluck('service_schedule')->flatten()->toArray());
                $booking['totalCount'] = count($booking['repeats']);
                $booking['bookingType'] = $booking['repeats'][0]['booking_type'];

                if ($booking['bookingType'] == 'weekly') {
                    $booking['weekNames'] = collect($booking['repeats'])
                        ->pluck('service_schedule')
                        ->map(function ($schedule) {
                            return \Carbon\Carbon::parse($schedule)->format('l');
                        })
                        ->unique()
                        ->sort()
                        ->values()
                        ->toArray();
                }

                $booking['completedCount'] = collect($booking['repeats'])->where('booking_status', 'completed')->count();
                $booking['canceledCount'] = collect($booking['repeats'])->where('booking_status', 'canceled')->count();

                unset($booking->repeat);
                $booking['repeats'] = array_map(function ($repeat) {
                    if (isset($repeat['repeat_histories'])) {
                        unset($repeat['repeat_histories']);
                    }
                    return $repeat;
                }, $booking['repeats']);
            }

            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function singleDetails(Request $request, string $id): JsonResponse
    {
        $booking = $this->bookingRepeat->with([
            'detail.service', 'scheduleHistories.user', 'statusHistories.user', 'booking.service_address', 'booking.customer', 'provider', 'serviceman.user'
        ])->where(['id' => $id])->first();

        if (isset($booking)) {
            if (isset($booking->provider)) {
                $booking->provider->chatEligibility = chatEligibility($booking->provider_id);
            }
            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function track(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking
            ->with(['detail.service', 'schedule_histories.user', 'status_histories.user', 'service_address', 'customer', 'provider', 'zone', 'serviceman.user'])
            ->where(['readable_id' => $id])
            ->whereHas('service_address', fn($query) => $query->where('contact_person_number', $request['phone']))
            ->first();

        if (isset($booking)) return response()->json(response_formatter(DEFAULT_200, $booking), 200);

        return response()->json(response_formatter(DEFAULT_404, $booking), 404);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $booking_id
     * @return JsonResponse
     */
    public function statusUpdate(Request $request, string $booking_id)
    {
        $validator = Validator::make($request->all(), [
            'booking_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $booking_id)->where('customer_id', $request->user()->id)->first();

        if (isset($booking)) {

            if ($booking->booking_status == 'accepted' && $request['booking_status'] == 'canceled') {
                return response()->json(response_formatter(BOOKING_ALREADY_ACCEPTED), 200);
            }

            if ($booking->booking_status == 'ongoing' && $request['booking_status'] == 'canceled') {
                return response()->json(response_formatter(BOOKING_ALREADY_ONGOING), 200);
            }

            if ($booking->booking_status == 'completed' && $request['booking_status'] == 'canceled') {
                return response()->json(response_formatter(BOOKING_ALREADY_COMPLETED), 200);
            }

            $booking->booking_status = $request['booking_status'];

            $bookingStatusHistory = $this->bookingStatusHistory;
            $bookingStatusHistory->booking_id = $booking_id;
            $bookingStatusHistory->changed_by = $request->user()->id;
            $bookingStatusHistory->booking_status = $request['booking_status'];

            DB::transaction(function () use ($bookingStatusHistory, $booking, $request) {
                $booking->save();
                $bookingStatusHistory->save();

                if ($request['booking_status'] == 'canceled' && $booking->repeat->isNotEmpty()) {
                    foreach ($booking->repeat as $repeat) {
                        $repeat->booking_status = 'canceled';
                        $repeat->setAttribute('skipNotification', false);
                        unset($repeat->skipNotification);
                        $repeat->save();

                        $repeatBookingStatusHistory = new $this->bookingStatusHistory;
                        $repeatBookingStatusHistory->booking_id = 0;
                        $repeatBookingStatusHistory->booking_repeat_id = $repeat->id;
                        $repeatBookingStatusHistory->changed_by = $request->user()->id;
                        $repeatBookingStatusHistory->booking_status = 'canceled';
                        $repeatBookingStatusHistory->save();
                    }
                }
            });

            return response()->json(response_formatter(BOOKING_STATUS_UPDATE_SUCCESS_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * @param Request $request
     * @param string $repeatId
     * @return JsonResponse
     */
    public function singleBookingCancel(Request $request, string $repeatId): JsonResponse
    {
        $customerId = $request->user()->id;
        $repeat = $this->bookingRepeat->where('id', $repeatId)->first();
        $bookingId = $repeat->booking_id;
        $booking = $this->booking->where('id', $bookingId)->where('customer_id', $customerId)->first();

        if ($booking && $repeat) {
            $statusCheck = $repeat->booking_status == 'canceled';
            if ($statusCheck) {
                return response()->json(response_formatter(BOOKING_ALREADY_CANCELED_200), 200);
            }

            DB::transaction(function () use ($repeat) {
                $repeat->booking_status = 'canceled';
                $repeat->save();
            });

            return response()->json(response_formatter(DEFAULT_200), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeOfflinePaymentData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'offline_payment_id' => 'required',
            'customer_information' => 'required',
            'booking_id' => 'required',
            'is_partial' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        // Retrieve booking
        $booking = $this->booking->find($request->booking_id);
        if (!$booking) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $offlinePaymentData = $this->offlinePayment->find($request['offline_payment_id']);
        if (!$offlinePaymentData) {
            return response()->json(response_formatter(DEFAULT_400, null, 'Invalid offline payment ID.'), 400);
        }

        $fields = array_column($offlinePaymentData->customer_information, 'field_name');
        $customerInformation = (array)json_decode(base64_decode($request['customer_information']))[0];

        foreach ($fields as $field) {
            if (!key_exists($field, $customerInformation)) {
                return response()->json(response_formatter(DEFAULT_400, $fields, null), 400);
            }
        }

        // Handle partial payment if applicable
        if ($request->is_partial) {
            $user = auth('api')->user();
            $walletBalance = $user->wallet_balance;

            if ($walletBalance <= 0 || $walletBalance >= $booking->total_booking_amount) {
                return response()->json(response_formatter(DEFAULT_400, null, 'Invalid partial payment data.'), 400);
            }

            $paidAmount = $walletBalance;
            $dueAmount = $booking->total_booking_amount - $paidAmount;

            // Save wallet payment
            BookingPartialPayment::create([
                'booking_id' => $booking->id,
                'paid_with' => 'wallet',
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
            ]);

            // Save remaining payment
            BookingPartialPayment::create([
                'booking_id' => $booking->id,
                'paid_with' => 'offline_payment',
                'paid_amount' => $dueAmount,
                'due_amount' => 0,
            ]);

            placeBookingTransactionForPartialDigital($booking);
        }

        // Check if the booking_id already exists
        $existingPayment = BookingOfflinePayment::where('booking_id', $request->booking_id)->first();

        $customerInformation = (array)json_decode(base64_decode($request['customer_information']))[0];

        if ($existingPayment) {
            // If it exists, update with new data
            $existingPayment->offline_payment_id = $request['offline_payment_id'];
            $existingPayment->method_name = OfflinePayment::find($request['offline_payment_id'])?->method_name;
            $existingPayment->customer_information = $customerInformation;
            $existingPayment->payment_status = 'pending';
            $existingPayment->save();
        } else {
            // If no existing record, create a new one
            $bookingOfflinePayment = new BookingOfflinePayment();
            $bookingOfflinePayment->booking_id = $request->booking_id;
            $bookingOfflinePayment->offline_payment_id = $request['offline_payment_id'];
            $bookingOfflinePayment->method_name = OfflinePayment::find($request['offline_payment_id'])?->method_name;
            $bookingOfflinePayment->customer_information = $customerInformation;
            $bookingOfflinePayment->payment_status = 'pending';
            $bookingOfflinePayment->save();
        }

        $booking->update(['payment_method' => 'offline_payment']);

        return response()->json(response_formatter(OFFLINE_PAYMENT_SUCCESS_200), 200);
    }

    public function switchPaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'payment_method' => 'required',
            'offline_payment_id' => 'required_if:payment_method,offline_payment',
            'customer_information' => 'required_if:payment_method,offline_payment',
            'is_partial' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        // Retrieve booking
        $booking = $this->booking->find($request->booking_id);
        if (!$booking) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        // Handle partial payment if applicable
        if ($request->is_partial) {
            $user = auth('api')->user();
            $walletBalance = $user->wallet_balance;

            if ($walletBalance <= 0 || $walletBalance >= $booking->total_booking_amount) {
                return response()->json(response_formatter(DEFAULT_400, null, 'Invalid partial payment data.'), 400);
            }

            $paidAmount = $walletBalance;
            $dueAmount = $booking->total_booking_amount - $paidAmount;

            // Save wallet payment
            BookingPartialPayment::create([
                'booking_id' => $booking->id,
                'paid_with' => 'wallet',
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
            ]);

            // Save remaining payment
            BookingPartialPayment::create([
                'booking_id' => $booking->id,
                'paid_with' => 'digital',
                'paid_amount' => $dueAmount,
                'due_amount' => 0,
            ]);
        }

        if ($request->payment_method == 'cash_after_service') {
            $booking->update(['payment_method' => 'cash_after_service', 'transaction_id' => 'cash-payment', 'is_verified' => 1]);
            if ($booking->booking_partial_payments->isNotEmpty()) {
                $booking->booking_partial_payments()
                    ->where('paid_with', '!=', 'wallet')
                    ->delete();
            }
            if ($request->is_partial) {
                placeBookingTransactionForPartialCas($booking);
            }

        } elseif ($request->payment_method == 'wallet_payment') {
            $booking->update(['payment_method' => 'wallet_payment', 'transaction_id' => 'wallet-payment']);
            placeBookingTransactionForWalletPayment($booking);

        } else {
            return response()->json(response_formatter(DEFAULT_400, null, 'Invalid payment method.'), 400);
        }

        return response()->json(response_formatter(PAYMENT_METHOD_UPDATE_200), 200);
    }

    public function digitalPaymentBookingResponse(Request $request): JsonResponse|array
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $payment_info = PaymentRequest::where('transaction_id', $request->transaction_id)->first();

        if (!$payment_info) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $additional_data = json_decode($payment_info->additional_data, true);

        $booking_repeat_id = $additional_data['booking_repeat_id'] ?? null;
        $register_new_customer = $additional_data['register_new_customer'] ?? 0;
        $new_user_phone = $register_new_customer == 1 ? $additional_data['phone'] : null;

        $booking = null;
        $booking_id = null;
        if (isset($payment_info) && $payment_info->attribute_id != null) {
            $booking = Booking::where('readable_id', $payment_info->attribute_id)->first();
            $booking_id = $booking ? $booking->id : null;
        }

        $loginToken = null;
        if ($register_new_customer == 1 && $new_user_phone != null) {
            $user = new User();
            $user->first_name = $additional_data['first_name'];
            $user->last_name = '';
            $user->phone = $additional_data['phone'];
            $user->password = bcrypt($additional_data['password']);
            $user->user_type = 'customer';
            $user->is_active = 1;
            $user->save();

            if ($user && $booking) {
                $booking->customer_id = $user->id;
                $booking->is_guest = 0;
                $booking->save();
            }

            $loginToken = $user->createToken('CUSTOMER_PANEL_ACCESS')->accessToken;
        }

        $response = [
            'booking_id' => $booking_id,
            'booking_repeat_id' => $booking_repeat_id,
            'new_user_phone' => $new_user_phone,
            'login_token' => $loginToken,
        ];

        return response()->json(response_formatter(DEFAULT_200, $response), 200);

    }


    public function bookingList(Request $request)
    {

        $serviceEnq = ServiceEnquiry::where('user_id', $request->user()->id)->get();

        $serviceEnq->map(function ($value) {

            $value->service_detail = Service::where('id', $value->service_id)->first();
        });
        return response()->json(response_formatter(DEFAULT_200, $serviceEnq), 200);
    }



    // public function bookAddons(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'booking_id' => 'required|uuid',
    //         'service_id' => 'required',
    //         'variant_key' => 'required',
    //         'quantity' => 'required',
    //         'service_cost' => 'required',
    //         'total_cost' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
    //     }

    //     $booking = $this->booking->where('id', $request->booking_id)->first();

    //     if (!isset($booking)) {
    //         return response()->json(response_formatter(DEFAULT_404, null, 'Booking not found.'), 404);
    //     }

    //     $detail = new BookingDetail();
    //     $detail->booking_id = $request->booking_id;
    //     $detail->service_id = $request->service_id;
    //     $detail->service_name = Service::find($request->service_id)->name ?? 'service-not-found';
    //     $detail->variant_key = $request->variant_key;
    //     $detail->quantity = $request->quantity;
    //     $detail->service_cost = $request->service_cost;
    //     $detail->discount_amount = 0.00;
    //     $detail->campaign_discount_amount = 0.00;
    //     $detail->overall_coupon_discount_amount = 0.00;
    //     $detail->tax_amount = 0.00;
    //     $detail->total_cost = $request->total_cost;
    //     $detail->is_addon = 1;
    //     $detail->save();

    //     return response()->json("Addons Booked success", 200);
    // }

    public function bookAddons(Request $request): JsonResponse
    {
        // Step 1: Validate request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|uuid|exists:bookings,id',
            'addons' => 'required|array|min:1',
            'addons.*.service_id' => 'required|uuid',
            'addons.*.variant_key' => 'required|string',
            'addons.*.service_name' => 'nullable|string|max:255',
            'addons.*.variation_id' => 'nullable|string',
            'addons.*.quantity' => 'required|numeric|min:1',
            'addons.*.service_cost' => 'required|numeric|min:0',
            'addons.*.total_cost' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $bookingId = $request->booking_id;

        // Step 2: Wrap everything in a transaction to ensure data consistency
        DB::beginTransaction();
        try {
            $booking = Booking::where('id', $bookingId)->first();
           
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found.',
                ], 404);
            }

            // Step 3: Get existing addon IDs for comparison
            $existingAddonIds = BookingDetail::where('booking_id', $bookingId)
                ->where('is_addon', 1)
                ->pluck('service_id')
                ->toArray();
                

            $newAddonIds = collect($request->addons)->pluck('service_id')->toArray();

            // Step 4: Remove all addons not present in the new request
            BookingDetail::where('booking_id', $bookingId)
                ->where('is_addon', 1)
                ->whereNotIn('service_id', $newAddonIds)
                ->delete();


            // Step 5: Reset and recalculate total addon amount
            $oldAddonsTotal = BookingDetail::where('booking_id', $bookingId)
                ->where('is_addon', 1)
                ->sum('total_cost');
           

            // Subtract old addons from booking total
            $booking->total_booking_amount -= $oldAddonsTotal;

            // Step 6: Update or insert each addon
            $newAddonsTotal = 0;
            foreach ($request->addons as $addon) {
                $service = CategoryExtra::find($addon['service_id']);
                $serviceName = $addon['service_name'] ?? $service?->name ?? 'Unnamed Service';

                BookingDetail::updateOrCreate(
                    [
                        'booking_id' => $bookingId,
                        'service_id' => $addon['service_id'],
                        'variant_key' => $addon['variant_key'],
                        'is_addon' => 1,
                    ],
                    [
                        'service_name' => $serviceName,
                        'quantity' => $addon['quantity'],
                        'service_cost' => $addon['service_cost'],
                        'discount_amount' => 0.00,
                        'campaign_discount_amount' => 0.00,
                        'overall_coupon_discount_amount' => 0.00,
                        'tax_amount' => 0.00,
                        'total_cost' => $addon['total_cost'],
                        'variation_id' => $addon['variation_id'] ?? null,
                    ]
                );

                $newAddonsTotal += $addon['total_cost'];
            }

            // Step 7: Update booking total and payment status
            $booking->total_booking_amount += $newAddonsTotal;
            $booking->is_paid = 0;
            $booking->due_amt_status = 0;
            $booking->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Addons updated successfully.',
                'data' => [
                    'booking_id' => $booking->id,
                    'total_booking_amount' => round($booking->total_booking_amount, 2),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while updating addons.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAddons(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sub_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $sub_category_id = $request->sub_category_id;

        $servicesQuery = $this->service
            ->with(['category.zonesBasicInfo', 'variations', 'service_discount', 'category.category_discount'])
            ->where('sub_category_id', $sub_category_id)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->whereDoesntHave('service_discount')
                    ->orWhereHas('service_discount')
                    ->orWhere(function ($query) {
                        $query->whereDoesntHave('category.category_discount')
                            ->orWhereHas('category.category_discount');
                    });
            })
            ->latest();

        $services = $servicesQuery->get();

        if (count($services) > 0) {

            return response()->json(response_formatter(DEFAULT_200, self::variationMapper($services)), 200);
        }

        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    public function getAddonStored(Request $request)
    {

        $bookings = BookingDetail::where('booking_id', $request->booking_id)->where('is_addon', 1)->get();

        return response()->json($bookings, 200);

    }

    public function getDueAmount(Request $request)
    {

        $booking = Booking::where('id', $request->booking_id)
            // ->where('due_amt_status', 0)
            ->first();

        if ($booking->payment_method == 'cash_after_service') {


            if ($booking->due_amt_status == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found or already settled.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Due amount retrieved successfully.',
                'due_amount' => round($booking->total_booking_amount, 2),
            ], 200);

        } else {

            if ($booking->due_amt_status == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found or already settled.',
                ], 404);
            }


            $paidAmount = BookingPartialPayment::where('booking_id', $request->booking_id)
                ->sum('paid_amount');

            $duePartialAmount = BookingPartialPayment::where('booking_id', $request->booking_id)
                ->sum('due_amount');

            $bookAmountAddon = BookingDetail::where('booking_id', $request->booking_id)
                ->where('is_addon', 1)
                ->sum('total_cost');

            $dueAmount = $bookAmountAddon + $duePartialAmount;


            return response()->json([
                'status' => true,
                'message' => 'Due amount retrieved successfully.',
                'due_amount' => round($dueAmount, 2),
            ], 200);
        }


    }

    public function updateDuePaymentSuccess(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|uuid',
            'paid_amount' => 'required|numeric|min:0',
        ]);


        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }


        $booking = Booking::where('id', $request->booking_id)
            ->where('due_amt_status', 0)
            ->first();

        if (!$booking) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        if ($request->has('payment_method')) {
            if ($request->payment_method == 'razor_pay') {
                $booking->payment_method = 'razor_pay';
            } else {
                $booking->payment_method = 'cash_after_service';
            }
            $booking->save();
        }
        if ($booking->payment_method == 'cash_after_service') {
            $booking->due_amt_status = 1;
            $booking->save();
            $provider = DB::table('providers')->where('id', $booking->provider_id)->first();
            $account = DB::table('accounts')->where('user_id', $provider->user_id)->first();
            $sharingPercentage = DB::table('business_settings')->where('key_name', 'default_commission')->first();
            $paid = (float)$request->paid_amount;
            $share = (float)$sharingPercentage->live_values;
            $net = $paid - (($paid * $share) / 100);
            if ($request->has('payment_method')) {
                if ($request->payment_method == 'razor_pay') {
                    DB::table('accounts')->where('user_id', $provider->user_id)->update([
//                'received_balance' => ($account->received_balance + round($net, 2)),
//                        'account_receivable' => ($account->account_receivable + ($paid - (($paid * $share) / 100))),
                    'account_payable' => round((($paid * $share) / 100), 2)
                    ]);
                }
            } else {
                DB::table('accounts')->where('user_id', $provider->user_id)->update([
//                'received_balance' => ($account->received_balance + round($net, 2)),
//                    'account_receivable' => ($account->account_receivable - (($paid * $share) / 100))
                    'account_payable' => round((($paid * $share) / 100), 2)
                ]);
            }
        } else {
//            $provider = DB::table('providers')->where('id', $booking->provider_id)->first();
//            $account = DB::table('accounts')->where('user_id', $provider->user_id)->first();
//            $sharingPercentage = DB::table('business_settings')->where('key_name', 'default_commission')->first();
//            $paid = (float)$request->paid_amount;
//            $share = (float)$sharingPercentage->live_values;
//            if ($request->payment_method == 'razor_pay') {
//                DB::table('accounts')->where('user_id', $provider->user_id)->update([
////                'received_balance' => ($account->received_balance + round($net, 2)),
//                    'account_receivable' => ($account->account_receivable + ($paid - (($paid * $share) / 100))),
//                    'received_balance' =>
//                ]);
//            }
            $paidAmount = BookingPartialPayment::where('booking_id', $request->booking_id)
                ->sum('paid_amount');

            $dueAmount = max(0, ($booking->total_booking_amount - $paidAmount));

            if ($request->paid_amount > $dueAmount) {
                return response()->json(response_formatter(DEFAULT_400, null, 'Paid amount exceeds due amount.'), 400);
            }

            BookingPartialPayment::create([
                'booking_id' => $booking->id,
                'paid_with' => 'digital',
                'paid_amount' => $request->paid_amount,
                'due_amount' => max(0, ($dueAmount - $request->paid_amount)),
            ]);


            $booking->due_amt_status = 1;
            $booking->save();

        }


        return response()->json(response_formatter(DEFAULT_200), 200);
    }


}
