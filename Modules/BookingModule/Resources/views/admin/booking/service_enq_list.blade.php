@extends('adminmodule::layouts.master')

@section('title',translate('Service Enquiry List'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/dataTables/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/dataTables/select.dataTables.min.css')}}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$status=='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{translate('all')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='pending'?'active':''}}"
                                   href="{{url()->current()}}?status=pending">
                                    Pending
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='done'?'active':''}}"
                                   href="{{url()->current()}}?status=done">
                                    Done
                                </a>
                            </li>
                        </ul>

                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">Total Enquiries:</span>
                            <span class="title-color">{{$enquiries->total()}}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->current()}}?status={{$status}}"
                                              class="search-form search-form_style-two"
                                              method="GET">
                                            <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <span class="material-icons">search</span>
                                            </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                       value="{{$search}}" name="search"
                                                       placeholder="{{translate('search_here')}}">
                                            </div>
                                            <button type="submit"
                                                    class="btn btn--primary">{{translate('search')}}</button>
                                        </form>

                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead>
                                            <tr>
                                                <th>{{translate('Sl')}}</th>
                                                <th>{{translate('Customer_Name')}}</th>
                                                <th class="text-center">Contact Details</th>
                                                <th class="text-center">Service Name</th>
                                                <th class="text-center">Date Time</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">{{translate('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($enquiries as $key => $enquiryData)
                                                <tr>
                                                    <td>{{$key+$enquiries->firstItem()}}</td>
                                                    <td>
                                                        {{$enquiryData->name}}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <a href="mailto:{{$enquiryData->email}}"
                                                                class="fz-12 fw-medium">
                                                                 {{$enquiryData->email ?? ''}}
                                                             </a>
                                                             <a href="tel:{{$enquiryData->mobile_number}}"
                                                                class="fz-12 fw-medium">
                                                                 {{$enquiryData->mobile_number ?? ''}}
                                                             </a>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">Test Service Name</td>
                                                    <td class="text-center">
                                                        {{date('d M, Y',strtotime($enquiryData->date))}}

                                                        {{date('H:i',strtotime($enquiryData->time))}}

                                                    </td>

                                                    <td>
                                                        <span class="badge {{ $enquiryData->status==0 ? 'badge-warning' : 'badge-success' }}">
                                                            {{ $enquiryData->status==0 ? 'Pending' : 'Done' }}
                                                        </span>
                                                    </td>


                                                    <td>
                                                        <div class="d-flex gap-2 justify-content-center">

                                                            <a href="{{route('admin.booking.enquiryView',[$enquiryData->id])}}"
                                                               class="action-btn btn--light-primary"
                                                               style="--size: 30px">
                                                                <span class="material-icons">visibility</span>
                                                            </a>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $enquiries->links() !!}
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
    <script src="{{asset('assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        "use strict"
        $(document).ready(function () {
            $('.js-select').select2();
        });

        $('.switcher_input').on('click', function () {
            let itemId = $(this).data('status');
            let route = '{{ route('admin.customer.status-update', ['id' => ':itemId']) }}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })
    </script>
    <script src="{{asset('assets/admin-module/plugins/dataTables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/admin-module/plugins/dataTables/dataTables.select.min.js')}}"></script>
@endpush
