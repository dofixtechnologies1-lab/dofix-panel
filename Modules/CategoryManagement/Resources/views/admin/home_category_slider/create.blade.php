@extends('adminmodule::layouts.master')

@section('title',translate('category_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/select2/select2.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/dataTables/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module/plugins/dataTables/select.dataTables.min.css')}}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('category_setup')}}</h2>
                    </div>

                    @can('category_add')
                        <div class="card category-setup mb-30">
                            <div class="card-body p-30">
                                <form action="{{route('admin.category.store')}}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                            <div class="d-flex flex-column">
                                                <select class="select-zone theme-input-style w-100" name="zone_ids[]"
                                                        multiple="multiple" id="zone_selector__select">
                                                    <option value="all">{{translate('Select All')}}</option>
                                                    @foreach($zones as $zone)
                                                        <option value="{{$zone['id']}}">{{$zone->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                            <div class="d-flex flex-column">
                                                <select class="select-zone theme-input-style w-100" name="category_id"
                                                       >
                                                    <option value="" disabled selected>Select Category</option>
                                                    @foreach($category as $categoryData)
                                                        <option value="{{$categoryData->id}}">{{$categoryData->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script src="{{asset('assets/category-module')}}/js/category/create.js"></script>
    <script src="{{asset('assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

    <script>
        "use strict"

        $('#zone_selector__select').on('change', function () {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('.status-update').on('click', function () {
            let itemId = $(this).data('status');
            let route = '{{route('admin.category.status-update',['id' => ':itemId'])}}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })

        $('.feature-update').on('click', function () {
            let itemId = $(this).data('featured');
            let route = '{{route('admin.category.featured-update',['id' => ':itemId'])}}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })
    </script>

@endpush
