@extends('adminmodule::layouts.master')

@section('title',translate('add_provider'))

@push('css_or_js')

    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/swiper/swiper-bundle.min.css')}}">

@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">

            <form action="{{route('admin.provider.store')}}" method="POST" enctype="multipart/form-data" id="">
                @csrf
                <h3>{{translate('Step 1')}}</h3>
                <section>
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('Add_New_Provider')}}</h2>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-4 create-provider-item mb-4">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="material-symbols-outlined icon-1">check</span>
                                    {{ translate('Basic info') }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="icon-2">2</span>
                                    {{ translate('Set Business Plan') }}
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="c1 mb-20">{{translate('General_Information')}}</h4>

                                <div class="col-md-6" id="register-form-p-0">
                            
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{old('company_name')}}"
                                                    name="company_name"
                                                    placeholder="{{translate('company_name')}}" required>
                                            <label>Company Name</label>
                                        </div>
                                    </div>
                                </div>   
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <select class="select-identity theme-input-style w-100" name="service_name" required>
                                                <option selected disabled>Select Service</option>
                                                @foreach($categories as $key => $category)
                                                    <option value="{{$category->id}}"
                                                        {{old('service_name') == $category->id ? 'selected': ''}}>
                                                        {{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{old('full_name')}}"
                                                    name="full_name"
                                                    placeholder="Full Name" required>
                                            <label>Full Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{old('contact_number')}}"
                                                    name="contact_number"
                                                    placeholder="Contact Number" required>
                                            <label>Contact Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{old('alt_contact_number')}}"
                                                    name="alt_contact_number"
                                                    placeholder="Alternate Contact Number" required>
                                            <label>Alternate Contact Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email"
                                                    name="email" value="{{old('email')}}"
                                                    placeholder="{{translate('Email')}}" required>
                                            <label>{{translate('Email')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <select class="select-identity theme-input-style w-100" name="zone_id" required>
                                                <option selected disabled>{{translate('Select_Zone')}}</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{$zone->id}}"
                                                        {{old('identity_type') == $zone->id ? 'selected': ''}}>
                                                        {{$zone->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                   
                    <div class="row gx-2 mt-2">
                        <div class="col-12 mt-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap justify-content-between gap-3 mb-20">
                                        <h4 class="c1">{{translate('Select Address from Map')}}</h4>
                                    </div>
                                    <div class="row gx-2">
                                        <div class="col-md-6 col-12">
                                            <div class="mb-30">
                                                <div class="form-floating form-floating__icon">
                                                    <input type="text" class="form-control" name="latitude"
                                                            id="latitude"
                                                            placeholder="{{translate('latitude')}} *"
                                                            value="" required readonly
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{translate('Select from map')}}">
                                                    <label>{{translate('latitude')}} *</label>
                                                    <span class="material-symbols-outlined">location_on</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="mb-30">
                                                <div class="form-floating form-floating__icon">
                                                    <input type="text" class="form-control" name="longitude"
                                                            id="longitude"
                                                            placeholder="{{translate('longitude')}} *"
                                                            value="" required readonly
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{translate('Select from map')}}">
                                                    <label>{{translate('longitude')}} *</label>
                                                    <span class="material-symbols-outlined">location_on</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-4">
                                            <div id="location_map_div" class="location_map_class">
                                                <input id="pac-input" class="form-control w-auto"
                                                        data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('search_your_location_here') }}"
                                                        type="text" placeholder="{{ translate('search_here') }}"/>
                                                <div id="location_map_canvas"
                                                        class="overflow-hidden rounded canvas_class"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                

                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="c1 mb-20">Adhar Details</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="adhar_number"
                                                            value="{{old('adhar_number')}}"
                                                            placeholder="Adhar Card Number" required>
                                                    <label>{{translate('Adhar Card Number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="adhar_img" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="c1 mb-20">Pan Card Details</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="pan_number"
                                                            value="{{old('pan_number')}}"
                                                            placeholder="Pan Card Number" >
                                                    <label>{{translate('Pan Card Number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="pan_img" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="c1 mb-20">Driving Licence Details</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="dl_number"
                                                            value="{{old('dl_number')}}"
                                                            placeholder="Driving licence number" >
                                                    <label>{{translate('Driving licence number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="dl_img" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="c1 mb-20">Profile Image</h4>
                            
                                    <div class="col-md-12">
                                        <input class="form-control" type="file" name="profile_img" required>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="c1 mb-20">Bank Details</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="account_hold_name"
                                                            value="{{old('account_hold_name')}}"
                                                            placeholder="Account holder name" required>
                                                    <label>Account Holder Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="branch_name"
                                                            value="{{old('branch_name')}}"
                                                            placeholder="Branch Name" required>
                                                    <label>Branch Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="acc_number"
                                                            value="{{old('acc_number')}}"
                                                            placeholder="Account Number" required>
                                                    <label>Account Number</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="ifsc_code"
                                                            value="{{old('ifsc_code')}}"
                                                            placeholder="IFSC Code" required>
                                                    <label>IFSC Code</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="bank_docs" required>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row gx-2 mt-2">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body"> 
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                     <input type="submit" class="btn btn--primary" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                {{-- <h3>{{translate('Step 2')}}</h3>
                <section>
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title mb-2">{{translate('Add New Provider')}}</h2>
                        <p class="page-title-text">{{translate('Setup Provider information and business plan from here')}} </p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-4 create-provider-item mb-4">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="material-symbols-outlined icon-1">check</span>
                                    {{translate('Basic info')}}
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="material-symbols-outlined icon-1">check</span>
                                    {{translate('Set Business Plan')}}
                                </div>
                            </div>

                            <h4>{{translate('Choose Business Plan')}}</h4>
                            <div class="col-sm-10 col-md-5 pt-1 pb-1">
                                <div class="border-bottom mt-3 mb-4"></div>
                            </div>
                            <div class="row g-4">
                                @if($commission)
                                    <div class="col-sm-6">
                                        <label class="input-radio-item">
                                            <input type="radio" class="subscription-type" name="plan_type" value="commission_based" checked>
                                            <div class="inner">
                                                <div class="w-0 flex-grow-1">
                                                    <h5>{{translate('Commission Base')}}</h5>
                                                    <p>
                                                        {{translate('You have to give a certain percentage of commission to admin for every booking request')}}
                                                    </p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                               
                                @if($subscription)
                                    <div class="col-sm-6">
                                        <label class="input-radio-item">
                                            <input type="radio" class="subscription-type" name="plan_type" value="subscription_based">
                                            <div class="inner">
                                                <div class="w-0 flex-grow-1">
                                                    <h5>{{translate('Subscription Base')}}</h5>
                                                    <p>
                                                        {{translate('You have to pay a certain amount in every month / year to admin as subscription fee')}}
                                                    </p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div id="subscription-based-plan" class="collapse">
                                <div class="pt-4">
                                    <div class="py-3">

                                        @if($subscription)
                                            <div class="priceBoxSwiper-wrap">
                                                <h3 class="font-bold text-center mb-4">Select Plan</h3>
                                                <div class="w-100">
                                                    <input type="hidden" name="selected_package_id" id="selected-package-input" value="">
                                                    <div dir="ltr" class="swiper price-box-slider">
                                                        <div class="swiper-wrapper">
                                                            @foreach($formattedPackages as $index => $package)
                                                                <div class="swiper-slide h-auto">
                                                                    <label class="d-block plan-item">
                                                                        <input type="radio" name="plan" id="{{ $package->id }}" {{ $index == 3 ? 'checked' : '' }} class="package-option" data-id="{{ $package->id }}">
                                                                        <div class="plan-item-inner">
                                                                            <div class="name">
                                                                                <div class="circle"></div>
                                                                                <span class="name-content">{{ $package->name }}</span>
                                                                            </div>
                                                                            <div class="price">{{ with_currency_symbol($package->price) }}</div>
                                                                            <span>{{ $package->duration }} {{translate('Days')}}</span>
                                                                            <ul class="info">
                                                                                @foreach($package->feature_list as $feature)
                                                                                    <li>{{ $feature }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="swiper-button-next"></div>
                                                        <div class="swiper-button-prev"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pt-0">
                                            <div class="text-center px-xl-4 pb-4">
                                                <img src="{{asset('assets/admin-module/img/provider-create.png')}}" alt="">
                                                <h4 class="mb-4 pb-3">{{translate('Select Payment Option')}}</h4>
                                                <div class="row g-3">
                                                    <div class="col-sm-12">
                                                        <label class="input-radio-item">
                                                            <input type="radio" name="plan_price" value="received_money" checked>
                                                            <div class="inner">
                                                                <div class="w-0 flex-grow-1">
                                                                    <h4 class="m-0 text-start">{{translate('Received Money Manually')}}</h4>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    @if($freeTrialStatus)
                                                        <div class="col-sm-12">
                                                            <label class="input-radio-item">
                                                                <input type="radio" name="plan_price" value="free_trial">
                                                                <div class="inner">
                                                                    <div class="w-0 flex-grow-1">
                                                                        <h4 class="m-0 text-start">{{translate('Continue with Free Trial')}} {{ $duration }} {{translate('days')}}</h4>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-4 flex-wrap justify-content-center mt-4 pt-2">
                                                    <button type="button" class="btn btn--secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                                                    <button type="button" class="btn btn--primary pay_complete_btn">{{translate('Complete')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section> --}}
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/provider-module')}}/js//tags-input.min.js"></script>
    <script src="{{asset('assets/provider-module')}}/js/spartan-multi-image-picker.js"></script>
    <script src="{{asset('assets/admin-module/plugins/swiper/swiper-bundle.min.js')}}"></script>

    <script src="{{asset('assets/provider-module')}}/plugins/jquery-steps/jquery.steps.min.js"></script>
    <script src="{{asset('assets/provider-module')}}/plugins/jquery-validation/jquery.validate.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{business_config('google_map', 'third_party')?->live_values['map_api_key_client']}}&libraries=places&v=3.45.8"></script>

    <script>
        "use strict";

            // function previewImage(event, previewId) {
            //     const reader = new FileReader();
            //     reader.onload = function() {
            //         const preview = document.getElementById(previewId);
            //         preview.src = reader.result;
            //     };
            //     reader.readAsDataURL(event.target.files[0]);
            // }

            function updateSelectedPackage() {
                const selectedPackage = document.querySelector('input[name="plan"]:checked');
                if (selectedPackage) {
                    document.getElementById('selected-package-input').value = selectedPackage.id;
                }
            }

            updateSelectedPackage();


        $(document).ready(function () {
            let formWizard = $("#create-provider-form");

            formWizard.validate({
                errorPlacement: function (error, element) {
                    element.parents('.form-floating, .form-error-wrap').after(error);
                },
            });

            formWizard.steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "fade",
                stepsOrientation: "vertical",
                autoFocus: true,
                labels: {
                    finish: "Submit",
                    next: "Proceed",
                    previous: "Back"
                },
                // onInit: function (event, currentIndex) {
                //     initializePhoneInput(
                //         ".phone-input-with-country-picker-provider-provider",
                //         ".country-picker-phone-number-provider-provider2-provider"
                //     );
                //     initializePhoneInput(
                //         ".phone-input-with-country-picker-provider-provider2",
                //         ".country-picker-phone-number-provider-provider2-provider2"
                //     );
                //     initializePhoneInput(
                //         ".phone-input-with-country-picker-provider-provider3",
                //         ".country-picker-phone-number-provider-provider2-provider3"
                //     );
                // },
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (newIndex < currentIndex) {
                        return true;
                    }

                    formWizard.validate().settings.ignore = ":disabled,:hidden";
                    let multiImg = $('.spartan_image_input');

                    if(multiImg.length < 2 && $('.spartan_item_wrapper_error_msg').length === 0) {
                        multiImg.closest('.spartan_item_wrapper > div').after('<div class="spartan_item_wrapper_error_msg error text-danger mt-2 fs-12">This field is required.</div>');
                    }

                    document.querySelectorAll('input[name="plan"]').forEach(function (input) {
                        input.addEventListener('change', updateSelectedPackage);
                    });

                    return formWizard.valid();
                },
                onFinished: function (event, currentIndex) {
                    const myModalAlternative = new bootstrap.Modal('#paymentModal', {});

                    if($('.subscription-type:checked').val() === 'subscription_based') {
                        myModalAlternative.show();

                        $('.pay_complete_btn').on('click', function() {
                            formWizard.submit();
                        })
                    } else {
                        formWizard.submit();
                    }
                }
            });

            $('.subscription-type').on('change', function(){
                if($(this).is(':checked')) {
                    if($(this).val() == 'commission_based') {
                        $('#subscription-based-plan').collapse('hide');
                    } else {
                        $('#subscription-based-plan').collapse('show');
                    }
                }
            })
            $(window).on('load', function(){
                $('.subscription-type').each(function(){
                    if($(this).is(':checked')) {
                        if($(this).val() == 'commission_based') {
                            $('#subscription-based-plan').collapse('hide');
                        } else {
                            $('#subscription-based-plan').collapse('show');
                        }
                    }
                })
            })

            let swiper = new Swiper(".price-box-slider", {
                slidesPerView: "auto",
                spaceBetween: 24,
                initialSlide: 0,
                autoWidth: true,
                loop: false,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
        });
    </script>

    <script>
        "use strict";

        $(document).ready(function () {
            $("#company_email").on("change keyup paste", function () {
                $('#account_email').val($(this).val());
            });
            // $("#company_phone").on("change keyup paste", function () {
                // const countryCode = $('#register-form-p-0').find('.iti__selected-dial-code').text();
                // $('#account_phone').val(`${countryCode} ${$(this).val()}`);
            // });

            setInterval(() => {
                // const countryCode = $('#register-form-p-0').find('.iti__selected-dial-code').text();
                // $('#account_phone').val(`${countryCode} ${$("#company_phone").val()}`);
                $('#account_email').val($('#company_email').val());
            }, 2000);
        });

    </script>

    <script>
        "use strict";

        $(document).ready(function () {
            // let imageCount = 0;
            // $("#multi_image_picker").spartanMultiImagePicker({
            //     fieldName: 'identity_images[]',
            //     maxCount: 2,
            //     allowedExt: 'png|jpg|jpeg',
            //     rowHeight: 'auto',
            //     groupClassName: 'item',
            //     dropFileLabel: "{{translate('Drop_here')}}",
            //     placeholderImage: {
            //         image: '{{asset('assets/admin-module')}}/img/media/banner-upload-file.png',
            //         width: '100%',
            //     },

            //     onRenderedPreview: function (index) {
            //         toastr.success('{{translate('Image_added')}}', {
            //             CloseButton: true,
            //             ProgressBar: true
            //         });
            //     },
            //     onAddRow: function (index) {
            //         $('.spartan_item_wrapper_error_msg').remove();
            //         imageCount++;
            //     },
            //     onRemoveRow: function (index) {
            //         imageCount--;
            //         if(imageCount == 1){
            //             $('.spartan_item_wrapper > div').after('<div class="spartan_item_wrapper_error_msg error text-danger mt-2 fs-12">This field is required.</div>');
            //         }
            //     },
            //     onExtensionErr: function (index, file) {
            //         toastr.error('{{translate('Please_only_input_png_or_jpg_type_file')}}', {
            //             CloseButton: true,
            //             ProgressBar: true
            //         });
            //     },
            //     onSizeErr: function (index, file) {
            //         toastr.error('{{translate('File_size_too_big')}}', {
            //             CloseButton: true,
            //             ProgressBar: true
            //         });
            //     }

            // });

            // function readURL(input) {
            //     if (input.files && input.files[0]) {
            //         var reader = new FileReader();

            //         reader.onload = function (e) {
            //             $('#viewer').attr('src', e.target.result);
            //         }

            //         reader.readAsDataURL(input.files[0]);
            //     }
            // }

            // $("#customFileEg1").change(function () {
            //     readURL(this);
            // });


            $(document).ready(function () {
                function initAutocomplete() {
                    var myLatLng = {

                        lat: 23.811842872190343,
                        lng: 90.356331
                    };
                    const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                        center: {
                            lat: 23.811842872190343,
                            lng: 90.356331
                        },
                        zoom: 13,
                        mapTypeId: "roadmap",
                    });

                    var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                    });

                    marker.setMap(map);
                    var geocoder = geocoder = new google.maps.Geocoder();
                    google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                        var coordinates = JSON.parse(coordinates);
                        var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                        marker.setPosition(latlng);
                        map.panTo(latlng);

                        document.getElementById('latitude').value = coordinates['lat'];
                        document.getElementById('longitude').value = coordinates['lng'];


                        geocoder.geocode({
                            'latLng': latlng
                        }, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[1]) {
                                    document.getElementById('address').innerHtml = results[1].formatted_address;
                                }
                            }
                        });
                    });

                    const input = document.getElementById("pac-input");
                    const searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                    map.addListener("bounds_changed", () => {
                        searchBox.setBounds(map.getBounds());
                    });
                    let markers = [];
                    searchBox.addListener("places_changed", () => {
                        const places = searchBox.getPlaces();

                        if (places.length == 0) {
                            return;
                        }
                        markers.forEach((marker) => {
                            marker.setMap(null);
                        });
                        markers = [];
                        const bounds = new google.maps.LatLngBounds();
                        places.forEach((place) => {
                            if (!place.geometry || !place.geometry.location) {
                                console.log("Returned place contains no geometry");
                                return;
                            }
                            var mrkr = new google.maps.Marker({
                                map,
                                title: place.name,
                                position: place.geometry.location,
                            });
                            google.maps.event.addListener(mrkr, "click", function (event) {
                                document.getElementById('latitude').value = this.position.lat();
                                document.getElementById('longitude').value = this.position.lng();
                            });

                            markers.push(mrkr);

                            if (place.geometry.viewport) {
                                bounds.union(place.geometry.viewport);
                            } else {
                                bounds.extend(place.geometry.location);
                            }
                        });
                        map.fitBounds(bounds);
                    });
                };
                initAutocomplete();
            });


            $('.__right-eye').on('click', function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active')
                    $(this).find('i').removeClass('tio-invisible')
                    $(this).find('i').addClass('tio-hidden-outlined')
                    $(this).siblings('input').attr('type', 'password')
                } else {
                    $(this).addClass('active')
                    $(this).siblings('input').attr('type', 'text')


                    $(this).find('i').addClass('tio-invisible')
                    $(this).find('i').removeClass('tio-hidden-outlined')
                }
            })
        });

    </script>

@endpush
