@extends('adminmodule::layouts.master')

@section('title',translate('provider_details'))

@push('css_or_js')

@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-3">
                <h2 class="page-title">{{translate('Provider_Details')}}</h2>
            </div>

            <div class="mb-3">
                <ul class="nav nav--tabs nav--tabs__style2">
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='overview'?'active':''}}"
                           href="{{url()->current()}}?web_page=overview">{{translate('Overview')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='subscribed_services'?'active':''}}"
                           href="{{url()->current()}}?web_page=subscribed_services">{{translate('Subscribed_Services')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='bookings'?'active':''}}"
                           href="{{url()->current()}}?web_page=bookings">{{translate('Bookings')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='serviceman_list'?'active':''}}"
                           href="{{url()->current()}}?web_page=serviceman_list">{{translate('Service_Man_List')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='settings'?'active':''}}"
                           href="{{url()->current()}}?web_page=settings">{{translate('Settings')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='bank_information'?'active':''}}"
                           href="{{url()->current()}}?web_page=bank_information">{{translate('Bank_Information')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='reviews'?'active':''}}"
                           href="{{url()->current()}}?web_page=reviews">{{translate('Reviews')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='subscription'?'active':''}}"
                           href="{{url()->current()}}?web_page=subscription&provider_id={{ request()->id }}">{{translate('Business Plan')}}</a>
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body p-30">
                    @if($provider->is_approved == 1)
                        <div class="provider-details-overview mb-30">
                            <div class="provider-details-overview__collect-cash">
                                <div class="statistics-card statistics-card__collect-cash h-100">
                                    <h3>{{translate('Collect_Cash_From_Provider')}}</h3>
                                    <h2>{{with_currency_symbol($provider->owner->account->account_payable ?? 0)}}</h2>
                                    @can('provider_update')
                                        <a href="{{route('admin.provider.collect_cash.list', [$provider->id])}}"
                                           class="btn btn--primary text-capitalize w-100 btn--lg mw-75">{{translate('Collect_Cash')}}</a>
                                    @endcan
                                </div>
                            </div>
                            <div class="provider-details-overview__statistics">

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__pending-withdraw">
                                    <h2>{{with_currency_symbol($provider->owner->account->balance_pending ?? 0)}}</h2>
                                    <h3>{{translate('Pending_Withdrawn')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__already-withdraw">
                                    <h2>{{with_currency_symbol($provider->owner->account->total_withdrawn ?? 0)}}</h2>
                                    <h3>{{translate('Already_Withdrawn')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__withdrawable-amount">
                                    <h2>{{with_currency_symbol($provider->owner->account->account_receivable ?? 0)}}</h2>
                                    <h3>{{translate('Withdrawable_Amount')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__total-earning">
                                    <h2>{{ with_currency_symbol($provider?->owner?->account?->received_balance ?? 0 + $provider?->owner?->account?->total_withdrawn ?? 0) }}</h2>
                                    <h3>{{translate('Total_Earning')}}</h3>
                                </div>
                            </div>
                            <div class="provider-details-overview__order-overview">
                                <div class="statistics-card statistics-card__order-overview h-100 pb-2">
                                    <h3 class="mb-0">{{translate('Booking_Overview')}}</h3>
                                    <div id="apex-pie-chart" class="d-flex justify-content-center"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center flex-wrap-reverse justify-content-between gap-3 mb-3">
                        <h2>{{translate('Information_Details')}}</h2>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            @if($provider->is_approved == 2)
                                <a type="button"
                                   class="btn btn-soft--danger text-capitalize provider_approval"
                                   id="button-deny-{{$provider->id}}" data-approve="{{$provider->id}}"
                                   data-status="deny">
                                    {{translate('Deny')}}
                                </a>
                            @endif
                            @if($provider->is_approved == 0 || $provider->is_approved == 2)
                                <a type="button" class="btn btn--success text-capitalize approval_provider"
                                   id="button-{{$provider->id}}" data-approve="{{$provider->id}}"
                                   data-approve="approve">
                                    {{translate('Accept')}}
                                </a>
                            @endif

                            @can('provider_update')
                                <a href="{{route('admin.provider.edit',[$provider->id])}}" class="btn btn--primary">
                                    <span class="material-icons">border_color</span>
                                    {{translate('Edit')}}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="information-details-box media flex-column flex-sm-row gap-20">
                                <img class="avatar-img radius-5" src="{{ $provider->logoFullPath }}" alt="{{ translate('logo') }}">
                                <div class="media-body ">
                                    <h5 class="information-details-box__title">Owner Name : {{Str::limit($provider->full_name, 30)}}</h5>
                            
                                    <ul class="contact-list">
                                        <li>
                                            <a href="tel:{{$provider->contact_number}}">Contact Number : {{$provider->contact_number}}</a>
                                        </li>
                                        <li>
                                            <a href="mailto:{{$provider->email}}">Email : {{$provider->email}}</a>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="information-details-box h-100">
                                <ul class="contact-list">
                                    <li>
                                        <a>Company Name : {{Str::limit($provider->company_name, 30)}}</a>
                                    </li>
                                    <li>
                                        <a href="tel:{{$provider->alt_contact_number}}">Alt. Contact Number : {{$provider->alt_contact_number}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="information-details-box">
                                <div class="row g-4">
                                    <div class="col-lg-3">
                                        <p><strong class="text-capitalize">
                                            Adhar Number -</strong> {{$provider->adhar_number}}
                                        </p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end"> 
                                            <a href="{{asset('/storage/provider/adhar_img/'.$provider->adhar_img)}}" data-lightbox="adhar-img" data-title="Aadhaar Image">
                                                <img width="100" class="max-height-100" src="{{asset('/storage/provider/adhar_img/'.$provider->adhar_img)}}" alt="Aadhaar Image">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-lg-3">
                                        <p><strong class="text-capitalize">
                                            Pan Number
                                            -</strong> {{$provider->pan_number}}
                                        </p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end"> 
                                            <a href="{{asset('/storage/provider/pan_img/'.$provider->pan_img)}}" data-lightbox="pan-img" data-title="Pan Image">
                                                <img width="100" class="max-height-100" src="{{asset('/storage/provider/pan_img/'.$provider->pan_img)}}" alt="Pan Image">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-lg-3">
                                        <p><strong class="text-capitalize">
                                            Driving Licence No.
                                            </strong> {{$provider->dl_number}}
                                        </p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end"> 
                                            <a href="{{asset('/storage/provider/dl_img/'.$provider->dl_img)}}" data-lightbox="dl-img" data-title="DL Image">
                                                <img width="100" class="max-height-100" src="{{asset('/storage/provider/dl_img/'.$provider->dl_img)}}" alt="DL Image">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-lg-3">
                                        <p>
                                            <strong class="text-capitalize">
                                            Profile Image-
                                            </strong> 
                                        </p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end"> 
                                            <a href="{{asset('/storage/provider/profile_img/'.$provider->profile_img)}}" data-lightbox="profile-img" data-title="Profile Image">
                                                <img width="100" class="max-height-100" src="{{asset('/storage/provider/profile_img/'.$provider->profile_img)}}" alt="Profile Image">
                                            </a> 
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
            
                                    <div class="col-lg-3">
                                        <p>
                                            <strong class="text-capitalize">
                                               Bank Docs - 
                                            </strong> 
                                        </p>
                                    </div>

                                    <div class="col-lg-12">  
                                        <a href="{{asset('/storage/provider/bank_docs/'.$provider->bank_docs)}}" data-lightbox="bank-img" data-title="Bank Image">
                                            <img width="100" class="max-height-100" src="{{asset('/storage/provider/profile_img/'.$provider->profile_img)}}" alt="Bank Image">
                                        </a>
                                    </div>



                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Add in <head> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />

    <!-- Add before </body> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>


    <script src="{{asset('assets/provider-module')}}/plugins/apex/apexcharts.min.js"></script>

    <script>
        "use strict";

        var options = {
            labels: ['accepted', 'ongoing', 'completed', 'canceled'],
            series: {{json_encode($total)}},
            chart: {
                width: 235,
                height: 160,
                type: 'donut',
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: "{{$provider->bookings_count}} Bookings",
                align: 'center',
                offsetX: 0,
                offsetY: 58,
                floating: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                },
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        show: true
                    }
                }
            }],
            legend: {
                position: 'bottom',
                offsetY: -5,
                height: 30,
            },
        };

        var chart = new ApexCharts(document.querySelector("#apex-pie-chart"), options);
        chart.render();

        $('.provider_approval').on('click', function () {
            let itemId = $(this).data('approve');
            let route = '{{ route('admin.provider.update-approval', ['id' => ':itemId', 'status' => 'deny']) }}';
            route = route.replace(':itemId', itemId);
            route_alert_reload(route, '{{ translate('want_to_deny_the_provider') }}');
        });

        $('.approval_provider').on('click', function () {
            let itemId = $(this).data('approve');
            let route = '{{ route('admin.provider.update-approval', ['id' => ':itemId', 'status' => 'approve']) }}';
            route = route.replace(':itemId', itemId);
            route_alert_reload(route, '{{ translate('want_to_approve_the_provider') }}');
        });
    </script>
@endpush
