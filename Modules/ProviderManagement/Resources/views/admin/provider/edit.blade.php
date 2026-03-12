@extends('adminmodule::layouts.master')

@section('title',translate('update_provider'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/swiper/swiper-bundle.min.css')}}">
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">

            <form action="{{route('admin.provider.update', [$provider->id])}}" method="POST" id=" "
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <section>
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('update_Provider')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-4 create-provider-item mb-4">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="material-symbols-outlined icon-1">check</span>
                                    {{ translate('Basic info') }}
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="c1 mb-20">{{translate('General_Information')}}</h4>

                                <div class="col-md-6" id="register-form-p-0">

                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{$provider->company_name}}"
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
                                                        {{$provider->service_id == $category->id ? 'selected': ''}}>
                                                        {{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" id="full_name" class="form-control" value="{{$provider->full_name}}"
                                                    name="full_name"
                                                    placeholder="Full Name" required>
                                            <label>Full Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{$provider->contact_number}}"
                                                    name="contact_number"
                                                    placeholder="Contact Number" required>
                                            <label>Contact Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="register-form-p-0">
                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="{{$provider->alt_contact_number ?? ''}}"
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
                                                    name="email" value="{{$provider->email ?? ''}}"
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
                                                        {{$provider->zone_id == $zone->id ? 'selected': ''}}>
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
                                                            value="{{$provider->coordinates['latitude'] ?? null}}" required readonly
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
                                                            value="{{$provider->coordinates['longitude'] ?? null}}" required readonly
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
                                                            value="{{$provider->adhar_number ?? ''}}"
                                                            placeholder="Adhar Card Number" required>
                                                    <label>{{translate('Adhar Card Number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="adhar_img">
                                            <img class="mt-3" src="{{asset('/storage/provider/adhar_img/'.$provider->adhar_img)}}" alt="Adhar Image" height="50px" width="70px">
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
                                                            value="{{$provider->pan_number ?? ''}}"
                                                            placeholder="Pan Card Number" >
                                                    <label>{{translate('Pan Card Number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="pan_img">
                                            <img class="mt-3" src="{{asset('/storage/provider/pan_img/'.$provider->pan_img)}}" alt="Pan Image" height="50px" width="70px">

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
                                                            value="{{$provider->dl_number ?? ''}}"
                                                            placeholder="Driving licence number" >
                                                    <label>{{translate('Driving licence number')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="dl_img">

                                            <img class="mt-3" src="{{asset('/storage/provider/dl_img/'.$provider->dl_img)}}" alt="DL Image" height="50px" width="70px">

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
                                        <input class="form-control" type="file" name="profile_img">
                                        <img class="mt-3" src="{{asset('/storage/provider/profile_img/'.$provider->profile_img)}}" alt="Profile Image" height="50px" width="70px">
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
                                                            value="{{$provider->bank_detail->acc_holder_name??''}}"
                                                            placeholder="Account holder name" required>
                                                    <label>Account Holder Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="branch_name"
                                                            value="{{$provider->bank_detail->branch_name ??''}}"
                                                            placeholder="Branch Name" required>
                                                    <label>Branch Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="acc_number"
                                                            value="{{$provider->bank_detail->acc_no??''}}"
                                                            placeholder="Account Number" required>
                                                    <label>Account Number</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="ifsc_code"
                                                            value="{{$provider->bank_detail->ifsc_code ?? ''}}"
                                                            placeholder="IFSC Code" required>
                                                    <label>IFSC Code</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <input class="form-control" type="file" name="bank_docs">

                                            <img class="mt-3" src="{{asset('/storage/provider/bank_docs/'.$provider->bank_docs)}}" alt="Bank Document" height="50px" width="70px">

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

            let initialPackageId = $('input[name="plan"]:checked').attr('data-id');

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
               /* onInit: function (event, currentIndex) {
                    initializePhoneInput(
                        ".phone-input-with-country-picker-provider",
                        ".country-picker-phone-number-provider"
                    );
                    initializePhoneInput(
                        ".phone-input-with-country-picker-provider2",
                        ".country-picker-phone-number-provider2"
                    );
                    initializePhoneInput(
                        ".phone-input-with-country-picker-provider3",
                        ".country-picker-phone-number-provider3"
                    );
                },*/
                onStepChanging: function (event, currentIndex, newIndex) {

                    if (newIndex < currentIndex) {
                        return true;
                    }

                    formWizard.validate().settings.ignore = ":disabled,:hidden";
                    let multiImg = $('.spartan_image_input');

                    if (multiImg.length < 2 && $('.spartan_item_wrapper_error_msg').length === 0) {
                        multiImg.closest('.spartan_item_wrapper > div').after('<div class="spartan_item_wrapper_error_msg error text-danger mt-2 fs-12">This field is required.</div>');
                    }

                    document.querySelectorAll('input[name="plan"]').forEach(function (input) {
                        input.addEventListener('change', updateSelectedPackage);
                    });


                    return formWizard.valid();
                },
                onFinished: function (event, currentIndex) {
                    const myModalAlternative = new bootstrap.Modal('#paymentModal', {});

                    let selectedPackageId = $('input[name="plan"]:checked').attr('data-id');

                    if ($('.subscription-type:checked').val() === 'subscription_based' && initialPackageId !== selectedPackageId) {
                        myModalAlternative.show();

                        $('.pay_complete_btn').on('click', function () {
                            formWizard.submit();
                        });
                    } else {
                        formWizard.submit();
                    }
                }
            });

            $('.subscription-type').on('change', function () {
                if ($(this).is(':checked')) {
                    if ($(this).val() == 'commission_based') {
                        $('#subscription-based-plan').collapse('hide');
                    } else {
                        $('#subscription-based-plan').collapse('show');
                    }
                }
            });

            $(window).on('load', function () {
                $('.subscription-type').each(function () {
                    if ($(this).is(':checked')) {
                        if ($(this).val() == 'commission_based') {
                            $('#subscription-based-plan').collapse('hide');
                        } else {
                            $('#subscription-based-plan').collapse('show');
                        }
                    }
                });
            });

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

        $(document).ready(function () {
            $("#company_email").on("change keyup paste", function () {
                $('#account_email').val($(this).val());
            });
            // $("#company_phone").on("change keyup paste", function () {
            //     const countryCode = $('#register-form-p-0').find('.iti__selected-dial-code').text();
            //     $('#account_phone').val(`${countryCode} ${$(this).val()}`);
            // });

            setInterval(() => {
                // const countryCode = $('#register-form-p-0').find('.iti__selected-dial-code').text();
                // $('#account_phone').val(`${countryCode} ${$("#company_phone").val()}`);
                $('#account_email').val($('#company_email').val());
            }, 2000);
        });

        $(document).ready(function () {


            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#viewer').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#customFileEg1").change(function () {
                readURL(this);
            });


            $(document).ready(function () {

                function initAutocomplete() {
                    var myLatLng = {

                        lat:{{$provider->coordinates['latitude'] ?? 23.811842872190343}},
                        lng:{{$provider->coordinates['longitude'] ?? 90.356331}}
                    };
                    const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                        center: {
                            lat:{{$provider->coordinates['latitude'] ?? 23.811842872190343}},
                            lng:{{$provider->coordinates['longitude'] ?? 90.356331}}
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



        $(document).ready(function () {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                console.warn("Speech Recognition not supported in this browser.");
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US';
            recognition.continuous = false;
            recognition.interimResults = false;

            const $input = $("#full_name");

            // Start voice recognition when input is focused
            $input.on("focus", function () {
                recognition.start();
            });

            // Stop listening when the user clicks away
            $input.on("blur", function () {
                recognition.stop();
            });

            // Put recognized speech into the input field
            recognition.addEventListener("result", function (event) {
                const transcript = event.results[0][0].transcript;
                $input.val(transcript);
            });

            recognition.addEventListener("end", function () {
                console.log("Speech recognition ended");
            });

            recognition.addEventListener("error", function (event) {
                console.error("Speech recognition error:", event.error);
            });
        });


    </script>

@endpush
