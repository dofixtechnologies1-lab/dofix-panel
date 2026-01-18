<?php

namespace Modules\Auth\Http\Controllers\Api\V1;

use App\Models\Otp;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessSettingsModule\Entities\SubscriptionPackage;
use Modules\CategoryManagement\Entities\Category;
use Modules\PaymentModule\Traits\SubscriptionTrait;
use Modules\PromotionManagement\Entities\PushNotification;
use Modules\PromotionManagement\Entities\PushNotificationUser;
use Modules\ProviderManagement\Emails\NewJoiningRequestMail;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ProviderManagement\Entities\BankDetail;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;

class RegisterController extends Controller
{
    protected Provider $provider;
    protected User $owner;
    protected User $user;
    protected Serviceman $serviceman;
    private SubscriptionPackage $subscriptionPackage;
    protected BankDetail $bank_detail;
    private Category $category;

    use SubscriptionTrait;

    public function __construct(Category $category, Provider $provider,BankDetail $bank_detail, User $owner, User $user, Serviceman $serviceman, SubscriptionPackage $subscriptionPackage)
    {
        $this->category = $category;
        $this->provider = $provider;
        $this->owner = $owner;
        $this->user = $user;
        $this->serviceman = $serviceman;
        $this->subscriptionPackage = $subscriptionPackage;
        $this->bank_detail = $bank_detail;
    }


     public function getCatgeory()
        { 
            $categories = $this->category
                ->ofStatus(1)
                ->ofFeatured(1) 
                ->latest();
            return response()->json(response_formatter(DEFAULT_200, $categories->get()), 200);

            foreach ($categories as $category) {
                $category->services_by_category = self::variationMapper($category->services_by_category);
            }
            $categories = $categories->get();


            return response()->json(response_formatter(DEFAULT_200, $categories), 200);
        }

    public function sendOtp(Request $request): JsonResponse
    {   
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'user_type'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userData=User::where('phone',$request->phone)->first();
        // dd($userData);

        if(!empty($userData) && $userData->user_type != $request->user_type){
            if($request->user_type == "customer"){
                return response()->json(['message' => "This number has already been registered as a technician"], 400);
            }
            else{
                return response()->json(['message' => "This number has already been registered as a customer"], 400);
            }
        }

        if($request->phone=="+919999999999" && $request->user_type == "customer"){
            $otp = 1234;
        }elseif($request->phone=="+919999900000" && $request->user_type == "provider-admin"){
            $otp = 1234;
        }
        else{
            $otp = rand(1111, 9999);
        }




        $expiresAt = now()->addMinute(1); // OTP valid for only 2 minute

        Otp::updateOrCreate(
            ['phone' => $request->phone], 
            ['otp' => $otp, 'is_verified' => false, 'expires_at' => $expiresAt]
        );

        // Send OTP via SMS or Email (integrate your OTP service here)
        // Example: SmsService::send($request->phone, "Your OTP is: $otp");

        sendPhoneOtp($otp, $request->phone); 

        return response()->json(['message' => 'OTP sent successfully',"OTP"=>$otp, "userData" => $userData], 200);

    }



    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $otpRecord = Otp::where('phone', $request->phone)
                    ->where('otp', $request->otp)
                    ->where('expires_at', '>', now())// Ensure OTP is not expired
                    ->first();

        if (!$otpRecord) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $otpRecord->update(['is_verified' => true]);
        $userData=User::where('phone',$request->phone)->first();

        if(empty($userData)){
            $user = $this->user;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->is_active = 1;
            $user->save();
        }
        
        $userData=User::where('phone',$request->phone)->first();

        

        $loginData = ['token' => $userData->createToken(CUSTOMER_PANEL_ACCESS)->accessToken, 'is_active' => $userData['is_active'] , 'RegisterComplete'=>$userData->is_phone_verified ==1 ? 1 : 0];
        
        return response()->json(response_formatter(REGISTRATION_200, $loginData), 200);


    }

    public function ChangeStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [ 
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userData = $this->user->where('phone', $request->phone)->update([
            'is_active' => (int) $request->is_active, 
        ]);
 
        return response()->json(['message' => 'Updated successfully', "userData" => $userData], 200);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function customerRegister(Request $request): JsonResponse
    { 
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            // 'password' => 'required|min:8',
            // 'gender' => 'in:male,female,others',
            // 'confirm_password' => 'required|same:password',
            // 'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 403);
        }


        $otpRecord = Otp::where('phone', $request->phone)->where('is_verified', true)->first();

        if (!$otpRecord) {
            return response()->json(['error' => 'OTP not verified'], 400);
        }

        if (!empty($$request['email']) && User::where('email', $request['email'])->exists()) {
            return response()->json(response_formatter(DEFAULT_400, null, [["error_code" => "email", "message" => translate('Email already taken')]]), 400);
        }
        if (!(User::where('phone', $request['phone'])->exists())) {
            return response()->json(response_formatter(DEFAULT_400, null, [["error_code" => "phone", "message" => "Phone Number Not Exist"]]), 400);
        }

        $user = $this->user->where('phone',$request->phone)->first();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email ?? '';
        $user->phone = $request->phone;
        $user->is_phone_verified = 1;
        // $user->profile_image = $request->has('profile_image') ? file_uploader('user/profile_image/', 'png', $request->profile_image) : 'default.png';
        // $user->date_of_birth = $request->date_of_birth;
        // $user->gender = $request->gender ?? 'male';
        // $user->password = bcrypt($request->password);
        $user->user_type = $request->user_type;
        $user->is_active = 1;

        if ($request->has('referral_code')) {
            $customerReferralEarning = business_config('customer_referral_earning', 'customer_config')->live_values ?? 0;
            $amount = business_config('referral_value_per_currency_unit', 'customer_config')->live_values ?? 0;
            $userWhoRerreded = User::where('ref_code', $request['referral_code'])->first();

            if (is_null($userWhoRerreded)) {
                return response()->json(response_formatter(REFERRAL_CODE_INVALID_400), 404);
            }

            if ($customerReferralEarning == 1 && isset($userWhoRerreded)){

                referralEarningTransactionDuringRegistration($userWhoRerreded, $amount);

                $userRefund  = isNotificationActive(null, 'refer_earn', 'notification', 'user');
                $title = get_push_notification_message('referral_code_used', 'customer_notification', $user?->current_language_key);
                if ($title && $userWhoRerreded->fcm_token && $userRefund) {
                    device_notification($userWhoRerreded->fcm_token, $title, null, null, null, 'general', null, $userWhoRerreded->id);
                }

                $pushNotification = new PushNotification();
                $pushNotification->title = translate('Your Referral Code Has Been Used!');
                $pushNotification->description = translate("Congratulations! Your referral code was used by a new user. Get ready to earn rewards when they complete their first booking.");
                $pushNotification->to_users = ['customer'];
                $pushNotification->zone_ids = [config('zone_id') == null ? $request['zone_id'] : config('zone_id')];
                $pushNotification->is_active = 1;
                $pushNotification->cover_image = asset('assets/admin/img/referral_2.png');
                $pushNotification->save();

                $pushNotificationUser = new PushNotificationUser();
                $pushNotificationUser->push_notification_id = $pushNotification->id;
                $pushNotificationUser->user_id = $userWhoRerreded->id;
                $pushNotificationUser->save();
            }
        }

        $user->referred_by = $userWhoRerreded->id ?? null;
        $user->save();

        // Delete OTP record after successful registration
        
        $otpRecord->delete();

        $phoneVerification = login_setup('phone_verification')?->value ?? 0;
        $emailVerification = login_setup('email_verification')?->value ?? 0;

        // if (!$phoneVerification && !$emailVerification){
            // $loginData = ['token' => $user->createToken(CUSTOMER_PANEL_ACCESS)->accessToken, 'is_active' => $user['is_active']];
            // return response()->json(response_formatter(REGISTRATION_200, $loginData), 200);
        // }

        return response()->json(response_formatter(REGISTRATION_200), 200);
    }



    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function providerRegister(Request $request): JsonResponse
    {       
        $validator = Validator::make($request->all(), [

            'company_name' => 'required',
            'full_name'=>'required',
            'contact_number'=>'required',
            'email'=>'required',
            'adhar_number'=>'required',
            'adhar_img' => 'required|image|mimes:jpeg,jpg,png,gif,heic,webp|max:10000',
            'pan_number'=>'nullable',
            'pan_img' => 'nullable|image|mimes:jpeg,jpg,png,gif,heic,webp|max:10000',
            'dl_number'=>'nullable',
            'dl_img' => 'nullable|image|mimes:jpeg,jpg,png,gif,heic,webp|max:10000',
            'acc_holder_name'=>'required',
            'acc_number'=>'required',
            'bank_doc' => 'required|image|mimes:jpeg,jpg,png,gif,heic,webp|max:10000',
            'branch_name'=>'required',
            'ifsc_code'=>'required',
            'profile_img'=>'required',
            'company_address'=>'required',
            'zone_id' => 'required|uuid',
            'latitude' => 'required',
            'longitude' => 'required',
            'passbook_img' => 'nullable|image|mimes:jpeg,jpg,png,gif,heic,webp|max:10000',
        ]);

        if ($validator->fails()) {
            // return response()->json("hii");
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if (User::where('email', $request['email'])->exists()) {
            // return response()->json("hii2");
            return response()->json(response_formatter(DEFAULT_400, null, [["error_code" => "account_email", "message" => translate('Email already taken')]]), 400);
        } 
        // if (User::where('phone', $request['contact_number'])->exists()) {
        //     // return response()->json($request['contact_number']);
        //     return response()->json(response_formatter(DEFAULT_400, null, [["error_code" => "account_phone", "message" => translate('Phone already taken')]]), 400);
        // }

        if ($request->choose_business_plan == 'subscription_base'){
            $package = $this->subscriptionPackage->where('id',$request->selected_package_id)->ofStatus(1)->first();
            $vatPercentage      = (int)((business_config('subscription_vat', 'subscription_Setting'))->live_values ?? 0);
            if (!$package){
                return response()->json(response_formatter(DEFAULT_400, null, [["error_code" => "package", "message" => translate('Please Select valid plan')]]), 400);
            }

            $id                 = $package->id;
            $price              = $package->price;
            $name               = $package->name;
            $vatAmount          = $package->price * ($vatPercentage / 100);
            $vatWithPrice       = $price + $vatAmount;
        }

        // $identityImages = [];
        // foreach ($request->identity_images as $image) {
        //     $imageName = file_uploader('provider/identity/', 'png', $image);
        //     $identityImages[] = ['image'=>$imageName, 'storage'=> getDisk()];
        // }
       
        $adhar_img = null;
        if ($request->hasFile('adhar_img')) {
            $adhar_img = file_uploader('provider/adhar_img/', 'png', $request->file('adhar_img'));
        }
        
        $pan_img = null;
        if ($request->hasFile('pan_img')) {
            $pan_img = file_uploader('provider/pan_img/', 'png', $request->file('pan_img'));
        }

        $dl_img = null;
        if ($request->hasFile('dl_img')) {
            $dl_img = file_uploader('provider/dl_img/', 'png', $request->file('dl_img'));
        }

        $profile_img = null;
        if ($request->hasFile('profile_img')) {
            $profile_img = file_uploader('provider/profile_img/', 'png', $request->file('profile_img'));
        }

        $bank_docs = null;
        if ($request->hasFile('bank_doc')) {
            $bank_docs = file_uploader('provider/cancelchequeImage/', 'png', $request->file('bank_doc'));
        }

        $passbook_img = null;
        if ($request->hasFile('passbook_img')) {
            $passbook_img = file_uploader('provider/passbook_img/', 'png', $request->file('passbook_img'));
        }

        $provider = $this->provider;
        $provider->company_name = $request->company_name ?? '';
        $provider->full_name = $request->full_name;
        $provider->contact_number = $request->contact_number;
        $provider->alt_contact_number = $request->alt_contact_number ?? '';
        $provider->email = $request->email ?? '';
        $provider->zone_id = $request->zone_id ?? '';
        $provider->coordinates = ['latitude' => $request->latitude, 'longitude' => $request->longitude];
        $provider->company_address = $request->company_address ?? '';
        $provider->adhar_number = $request->adhar_number;
        $provider->adhar_img = $adhar_img ?? '';
       
        $provider->pan_number = $request->pan_number;
        $provider->pan_img = $pan_img ?? '';

        $provider->dl_number = $request->dl_number;
        $provider->dl_img = $dl_img ?? '';

        $provider->passbook_img = $passbook_img ?? '';

        $provider->profile_img = $profile_img ?? '';
        $provider->bank_docs = $bank_docs ?? '';
        $provider->is_approved = 1;
        $provider->is_active = 1;
        $provider->service_id = $request->category_id ?? null;


        $owner = $this->owner::where('phone',$request->contact_number)->first();
        $owner->first_name = $request->full_name;
        $owner->last_name = '';
        $owner->email = $request->email;
        $owner->phone = $request->contact_number;
        $owner->profile_image = $profile_img ?? '';
        // $owner->identification_number = $request->identity_number;
        // $owner->identification_type = $request->identity_type;
        $owner->is_phone_verified = 1;
        $owner->password = bcrypt(12345678);
        $owner->user_type = 'provider-admin';
        $owner->is_active = 0;

        DB::transaction(function () use ($provider, $owner, $request) {
            $owner->save();
            $provider->user_id = $owner->id;
            $provider->save();
        });
 
        $cancelchequeImage = null;
        if ($request->hasFile('cancelchequeImage')) {
            $cancelchequeImage = file_uploader('provider/cancelchequeImage/', 'png', $request->file('cancelchequeImage'));
        }


        $this->bank_detail::updateOrCreate(
            ['provider_id' => $provider?->id],
            [
                'bank_name' => $request->branch_name,
                'branch_name' => $request->branch_name,
                'acc_no' => $request->acc_number,
                'acc_holder_name' => $request->acc_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'cancelchequeImage' => $cancelchequeImage,
            ]
        );


        if ($request->choose_business_plan == 'subscription_base') {
            $provider_id = $provider->id;
            if ($request->free_trial_or_payment == 'free_trial') {
                $result = $this->handleFreeTrialPackageSubscription($id, $provider_id, $price, $name);
                if (!$result){
                    return response()->json(response_formatter(DEFAULT_FAIL_200), 400);
                }
            }elseif ($request->free_trial_or_payment == 'payment') {
                $paymentUrl = url('payment/subscription') . '?' .
                    'provider_id=' . $provider_id . '&' .
                    'access_token=' . base64_encode($owner->id) . '&' .
                    'package_id=' . $id . '&' .
                    'amount=' . $vatWithPrice . '&' .
                    'name=' . $name . '&' .
                    'package_status=' . 'subscription_purchase' . '&' .
                    http_build_query($request->all());
                return response()->json(response_formatter(PROVIDER_STORE_200, $paymentUrl), 200);
            }
        }

        return response()->json(response_formatter(PROVIDER_STORE_200), 200);
    }


    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user_verification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $data = DB::table('user_verifications')
            ->where('identity', $request['identity'])
            ->where(['otp' => $request['otp']])->first();

        if (isset($data)) {
            $this->user->whereIn('user_type', CUSTOMER_USER_TYPES)
                ->where('phone', $request['identity'])
                ->update([
                    'is_phone_verified' => 1
                ]);

            DB::table('user_verifications')
                ->where('identity', $request['identity'])
                ->where(['otp' => $request['otp']])->delete();

            return response()->json(response_formatter(DEFAULT_VERIFIED_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

   

}
