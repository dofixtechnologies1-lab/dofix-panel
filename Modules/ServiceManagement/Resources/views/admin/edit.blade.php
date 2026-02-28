@extends('adminmodule::layouts.master')

@section('title',translate('service_update'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/wysiwyg-editor/froala_editor.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/css/tags-input.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('update_service')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <form action="{{route('admin.service.update',[$service->id])}}" method="post"
                                  enctype="multipart/form-data"
                                  id="service-add-form">
                                @csrf
                                @method('PUT')
                                @php($language= Modules\BusinessSettingsModule\Entities\BusinessSettings::active()->where('key_name','system_language')->first())
                                @php($default_lang = str_replace('_', '-', app()->getLocale()))
                                @if($language)
                                    <ul class="nav nav--tabs border-color-primary mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                               href="#"
                                               id="default-link">{{translate('default')}}</a>
                                        </li>
                                        @foreach ($language?->live_values as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                   href="#"
                                                   id="{{ $lang['code'] }}-link">{{ get_language_name($lang['code']) }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div id="form-wizard">
                                    <h3>{{translate('service_information')}}</h3>
                                    <section>
                                        <div class="row">
                                            <div class="col-lg-5 mb-5 mb-lg-0">
                                                @if($language)
                                                    <div class="form-floating form-floating__icon mb-30 lang-form" id="default-form">
                                                        <input type="text" name="name[]" class="form-control"
                                                               placeholder="{{translate('service_name')}}"
                                                               value="{{$service?->getRawOriginal('name')}}" required>
                                                        <label>{{translate('service_name')}} ({{ translate('default') }}
                                                            )</label>
                                                        <span class="material-icons">subtitles</span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                    @foreach ($language?->live_values as $lang)
                                                            <?php
                                                            if (count($service['translations'])) {
                                                                $translate = [];
                                                                foreach ($service['translations'] as $t) {
                                                                    if ($t->locale == $lang['code'] && $t->key == "name") {
                                                                        $translate[$lang['code']]['name'] = $t->value;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        <div class="form-floating form-floating__icon mb-30 d-none lang-form"
                                                             id="{{$lang['code']}}-form">
                                                            <input type="text" name="name[]" class="form-control"
                                                                   placeholder="{{translate('service_name')}}"
                                                                   value="{{$translate[$lang['code']]['name']??''}}">
                                                            <label>{{translate('service_name')}}
                                                                ({{strtoupper($lang['code'])}})</label>
                                                            <span class="material-icons">subtitles</span>
                                                        </div>
                                                        <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                                    @endforeach
                                                @else
                                                    <div class="lang-form">
                                                        <div class="mb-30">
                                                            <div class="form-floating form-floating__icon">
                                                                <input type="text" class="form-control" name="name[]"
                                                                       placeholder="{{translate('service_name')}} *"
                                                                       required value="{{$service->name}}">
                                                                <label>{{translate('service_name')}} *</label>
                                                                <span class="material-icons">subtitles</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                @endif
                                                <div class="mb-30">
                                                    <select class="js-select theme-input-style w-100" name="category_id"
                                                            id="category-id">
                                                        <option value="0" selected
                                                                disabled>{{translate('choose_category')}}</option>
                                                        @foreach($categories as $category)
                                                            <option
                                                                value="{{$category->id}}" {{$category->id==$service->category_id?'selected':''}}>
                                                                {{$category->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-30" id="sub-category-selector">
                                                    <select class="js-select theme-input-style w-100"
                                                            name="sub_category_id"></select>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating form-floating__icon">
                                                        <input type="hidden" class="form-control" name="tax" min="0"
                                                               max="100" step="0.01"
                                                               placeholder="{{translate('add_tax_percentage')}} *"
                                                               required="" value="{{$service->tax}}">
                                                        {{-- <label>{{translate('add_tax_percentage')}} *</label>
                                                        <span class="material-icons">percent</span> --}}
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating form-floating__icon">
                                                        <input type="hidden" class="form-control"
                                                               name="min_bidding_price" min="0"
                                                               max="100" step="any"
                                                               placeholder="Service Starting Price *"
                                                               required="" value="{{$service->min_bidding_price}}">
                                                        {{-- <label>Service Starting Price *</label>
                                                        <span class="material-icons">price_change</span> --}}
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="tags"
                                                               placeholder="{{translate('Enter tags')}}"
                                                               value="{{implode(",",$tagNames)}}"
                                                               data-role="tagsinput">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-5 mb-5 mb-sm-0">
                                                <div class="d-flex flex-column align-items-center gap-3">
                                                    <p class="mb-0">{{translate('thumbnail_image')}}</p>
                                                    <div>
                                                        <div class="upload-file">
                                                            <input type="file" class="upload-file__input"
                                                                   name="thumbnail" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                            <div class="upload-file__img">
                                                                <img src="{{$service->thumbnail_full_path}}"
                                                                     alt="{{translate('image')}}">
                                                            </div>
                                                            <span class="upload-file__edit">
                                                                <span class="material-icons">edit</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg, png,
                                                        jpeg,
                                                        gif Image
                                                        Size -
                                                        maximum size 2 MB Image Ratio - 1:1')}}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-7">
                                                <div class="d-flex flex-column align-items-center gap-3">
                                                    <p class="mb-0">{{translate('cover_image')}}</p>
                                                    <div>
                                                        <div class="upload-file">
                                                            <input type="file" class="upload-file__input"
                                                                   name="cover_image" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                            <div class="upload-file__img upload-file__img_banner">
                                                                <img alt="{{ translate('cover-image') }}"
                                                                     src="{{$service->cover_image_full_path}}">
                                                            </div>
                                                            <span class="upload-file__edit">
                                                                <span class="material-icons">edit</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg, png,
                                                        jpeg, gif Image Size - maximum size 2 MB Image Ratio - 3:1')}}</p>
                                                </div>
                                            </div>

                                            @if($language)
                                                <div class="lang-form2" id="default-form2">
                                                    <div class="col-lg-12 mt-5">
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <textarea type="text" class="form-control" required
                                                                          name="short_description[]">{{$service?->getRawOriginal('short_description')}}</textarea>
                                                                <label>{{translate('short_description')}}
                                                                    ({{translate('default')}}) *</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4 mt-md-5">
                                                        <label for="editor"
                                                               class="mb-2">{{translate('long_Description')}}
                                                            ({{translate('default')}})
                                                            <span class="text-danger">*</span></label>
                                                        <section id="editor" class="dark-support">
                                                            <textarea class="ckeditor" required
                                                                      name="description[]">{!! $service?->getRawOriginal('description') !!}</textarea>
                                                        </section>
                                                    </div>
                                                </div>
                                                @foreach ($language?->live_values as $lang)
                                                        <?php
                                                        if (count($service['translations'])) {
                                                            $translate = [];
                                                            foreach ($service['translations'] as $t) {
                                                                if ($t->locale == $lang['code'] && $t->key == "short_description") {
                                                                    $translate[$lang['code']]['short_description'] = $t->value;
                                                                }

                                                                if ($t->locale == $lang['code'] && $t->key == "description") {
                                                                    $translate[$lang['code']]['description'] = $t->value;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    <div class="d-none lang-form2" id="{{$lang['code']}}-form2">
                                                        <div class="col-lg-12 mt-5">
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                        <textarea type="text" class="form-control"
                                                                  name="short_description[]">{{$translate[$lang['code']]['short_description']??''}}</textarea>
                                                                    <label>{{translate('short_description')}}
                                                                        ({{strtoupper($lang['code'])}}) *</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-4 mt-md-5">
                                                            <label for="editor"
                                                                   class="mb-2">{{translate('long_Description')}}
                                                                ({{strtoupper($lang['code'])}})
                                                                <span class="text-danger">*</span></label>
                                                            <section id="editor" class="dark-support">
                                                                <textarea class="ckeditor"
                                                                          name="description[]">{!! $translate[$lang['code']]['description']??'' !!}</textarea>
                                                            </section>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else

                                                <div class="normal-form">
                                                    <div class="col-lg-12 mt-5">
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <textarea type="text" class="form-control" required
                                                                          name="short_description[]">{!! $service->short_description !!}</textarea>
                                                                <label>{{translate('short_description')}} *</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4 mt-md-5">
                                                        <label for="editor"
                                                               class="mb-2">{{translate('long_Description')}}
                                                            <span class="text-danger">*</span></label>
                                                        <section id="editor" class="dark-support">
                                                            <textarea class="ckeditor" required
                                                                      name="description[]">{!! $service->description !!}</textarea>
                                                        </section>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </section>

                                    <h3>{{translate('price_variation')}}</h3>
                                    <section>
                                        <div class="d-flex flex-wrap  mb-3 row">
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="text" class="form-control" name="variant_name"
                                                       id="variant-name"
                                                       placeholder="{{translate('add_variant')}} *" required="">
                                                <label>{{translate('add_variant')}} *</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="number" class="form-control" name="mrp_price"
                                                       id="mrp-price"
                                                       placeholder="MRP Price 123 *" value="0">
                                                <label>MRP Price *</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="number" class="form-control" name="discount"
                                                       id="discount-percent"
                                                       placeholder="Discount Percent *" value="0">
                                                <label>Discount Percent %*</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="number" class="form-control" name="variant_price"
                                                       id="variant-price"
                                                       placeholder="{{translate('price')}} *" value="0">
                                                <label>{{translate('price')}} *</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="number" class="form-control" name="convenience_fee"
                                                       id="convenience-fee"
                                                       placeholder="Convenience Fee *" value="0">
                                                <label>Convenience Fee *</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                {{-- <input type="number" class="form-control" name="convenience_gst"
                                                       id="convenience-gst"
                                                       placeholder="Convenience GST *" value="0">
                                                <label>Convenience GST % *</label> --}}
                                                <select class="js-select theme-input-style w-100 form-error-wrap" name="convenience_gst" id="convenience-gst">
                                                    <option value="0" selected disabled>Select Tax * </option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{$tax->id}}">{{$tax->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="number" class="form-control" name="aggregator_fee"
                                                       id="aggregator-fee"
                                                       placeholder="Aggregator Fee *" value="0">
                                                <label>Aggregator Fee *</label>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                {{-- <input type="number" class="form-control" name="aggregator_gst"
                                                       id="aggregator-gst"
                                                       placeholder="Aggregator GST *" value="0">
                                                <label>Aggregator GST %*</label> --}}
                                                <select class="js-select theme-input-style w-100 form-error-wrap" name="aggregator_gst" id="aggregator-gst">
                                                    <option value="0" selected disabled>Select Tax * </option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{$tax->id}}">{{$tax->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="text" class="form-control" name="var_description"
                                                       id="var_description"
                                                       placeholder="Enter description">
                                                <label>Description</label>
                                            </div>
                                            {{-- <div class=" col-lg-4 col-md-6 form-floating mb-3 ">
                                                <input type="time" class="form-control" name="var_duration"
                                                       id="var_duration" >
                                                <label>Select Duration</label>
                                            </div> --}}

                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <h5 for="duration-hour" class="form-label">Select Duration</h5>
                                                <div class="d-flex gap-2 mt-2">
                                                    <!-- Hour Dropdown -->
                                                    <select class="js-select theme-input-style w-50 form-error-wrap" name="duration_hour" id="duration_hour">
                                                        <option value="" selected>Hour</option>
                                                        @for($i = 0; $i < 24; $i++)
                                                            <option value="{{ $i }}">{{ $i }} {{ Str::plural('Hour', $i) }}</option>
                                                        @endfor
                                                    </select>

                                                    <!-- Minute Dropdown -->
                                                    <select class="js-select theme-input-style w-50 form-error-wrap" name="duration_minute" id="duration_minute">
                                                        <option value="" selected>Minute</option>
                                                        @for($i = 0; $i < 60; $i++)
                                                            <option value="{{ $i }}">{{ $i }} {{ Str::plural('Minute', $i) }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="file" class="form-control" name="var_image"
                                                       accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*"
                                                       id="var_image">
                                                <label for="var_image">Upload Image</label>
                                            </div>


                                            <div class=" col-lg-6 col-md-6  mt-3 ">
                                            <button type="button" class="btn btn--primary" id="service-ajax-variation">
                                                <span class="material-icons">add</span>
                                                {{translate('add')}}
                                            </button>
                                            </div>
                                        </div>

                                        <div class="table-responsive p-01">
                                            <table class="table align-middle table-variation">
                                                <thead id="category-wise-zone" class="text-nowrap">
                                                <tr>
                                                    <th scope="col">{{translate('variations')}}</th>
                                                    <th scope="col">{{translate('default_price')}}</th>
                                                    {{-- @foreach($zones as $zone)
                                                        <th scope="col">{{$zone->name}}</th>
                                                    @endforeach --}}
                                                    <th scope="col">{{translate('Cover Image')}}</th>
                                                    <th scope="col">{{translate('action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="variation-update-table">
                                                @include('servicemanagement::admin.partials._update-variant-data',['variants'=>$service->variations,'zones'=>$zones])
                                                </tbody>
                                            </table>

                                            <div id="new-variations-table"
                                                 class="{{session()->has('variations') && count(session('variations'))>0?'':'hide-div'}}">
                                                <label
                                                    class="badge badge-primary mb-10">{{translate('new_variations')}}</label>
                                                <table class="table align-middle table-variation">
                                                    <tbody id="variation-table">
                                                    @include('servicemanagement::admin.partials._variant-data',['zones'=>$zones])
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editVariantModal" tabindex="-1" aria-labelledby="editVariantModalLabel"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVariantModalLabel">Edit Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="edit-variant-modal-content" class="row">
                    Loading...
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/admin-module')}}/js//tags-input.min.js"></script>
    <script src="{{asset('assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script src="{{asset('assets/admin-module')}}/plugins/jquery-steps/jquery.steps.min.js"></script>
    <script src="{{asset('assets/admin-module/plugins/tinymce/tinymce.min.js')}}"></script>
    <script src="{{asset('assets/ckeditor/jquery.js')}}"></script>
    <script>
        $(document).ready(function() {

            // Run this when modal is fully shown
            $('#editVariantModal').on('shown.bs.modal', function () {

                function calculatePrice() {
                    let mrp = parseFloat($(this).closest('form').find('input[name="mrp_price"]').val()) || 0;
                    let discount = parseFloat($(this).closest('form').find('input[name="discount"]').val()) || 0;
                    let finalPrice = mrp - (mrp * (discount / 100));
                    finalPrice = finalPrice.toFixed(2);
                    $(this).closest('form').find('input[name="variant_price"]').val(finalPrice);
                }

                // Bind on blur inside the modal
                $(this).find('input[name="mrp_price"], input[name="discount"]').off('blur').on('blur', calculatePrice);
            });

        });
    </script>

    <script>
        "use strict";

        $(document).ready(function () {
            $('.js-select').select2();
        });



        $("#form-wizard").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            autoFocus: true,
            onFinished: function (event, currentIndex) {
                $("#service-add-form")[0].submit();
            }
        });

        ajax_get('{{url('/')}}/admin/category/ajax-childes-only/{{$service->category_id}}?sub_category_id={{$service->sub_category_id}}', 'sub-category-selector')

        $("#service-ajax-variation").on('click', function () {
            let route = "{{route('admin.service.ajax-add-variant')}}";
            let id = "variation-table";
            ajax_variation(route, id);
        })

        function ajax_variation(route, id) {

            let name = $('#variant-name').val();
            let price = $('#variant-price').val();
            let mrpPrice = $('#mrp-price').val();
            let discount = $('#discount-percent').val();
            let convenienceFee = $('#convenience-fee').val();
            let aggregatorFee = $('#aggregator-fee').val();
            let file = $('#var_image')[0].files[0];

            if (name.length > 0 && price >= 0 && mrpPrice >= 0 && discount >= 0 && convenienceFee >= 0  && aggregatorFee >= 0) {
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}'); // important for Laravel
                formData.append('name', name);
                formData.append('price', price);
                formData.append('mrp_price', mrpPrice);
                formData.append('discount_percent', discount);
                formData.append('convenience_fee', convenienceFee);
                formData.append('convenience_gst', $('#convenience-gst').val());
                formData.append('aggregator_fee', aggregatorFee);
                formData.append('aggregator_gst', $('#aggregator-gst').val());
                formData.append('var_description', $('#var_description').val());
                formData.append('var_duration', $('#var_duration').val());
                formData.append('duration_hour', $('#duration_hour').val());
                formData.append('duration_minute', $('#duration_minute').val());
        
                if(file) formData.append('var_image', file);
        
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function () { /*$('#loading').show();*/ },
                    success: function (response) {
                        if (response.flag == 0) {
                            toastr.info('Already added');
                        } else {
                            $('#new-variations-table').show();
                            $('#' + id).html(response.template);
        
                            // reset fields
                            $('#variant-name').val("");
                            $('#variant-price').val(0);
                            $('#mrp-price').val(0);
                            $('#discount-percent').val(0);
                            $('#convenience-fee').val(0);
                            $('#convenience-gst').val("");
                            $('#aggregator-fee').val(0);
                            $('#aggregator-gst').val("");
                            $('#var_description').val("");
                            $('#var_duration').val("");
                            $('#duration_hour').val("");
                            $('#duration_minute').val("");
                            $('#var_image').val("");
                        }
                    },
                    complete: function () { /*$('#loading').hide();*/ },
                    error: function(err) {
                        toastr.error('Upload failed!');
                    }
                });
            } else {
                toastr.warning('{{translate('fields_are_required')}}');
            }
        }

        document.addEventListener('click', function(event) {
            if (event.target.closest('.service-ajax-remove-variant')) {
                var route = event.target.closest('.service-ajax-remove-variant').getAttribute('data-route');
                var id = event.target.closest('.service-ajax-remove-variant').getAttribute('data-id');
                ajax_remove_variant(route, id);
            }
        });


        function ajax_remove_variant(route, id) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: "{{translate('want_to_remove_this_variation')}}",
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        dataType: 'json',
                        data: {},
                        beforeSend: function () {
                        },
                        success: function (response) {
                            console.log(response.template)
                            $('#' + id).html(response.template);
                        },
                        complete: function () {
                        },
                    });
                }
            })
        }


        $("#category-id").change(function () {
            let id = this.value;
            let route = "{{ url('/admin/category/ajax-childes/') }}/" + id;
            ajax_switch_category(route)
        });

        function ajax_switch_category(route) {
            $.get({
                url: route + '?service_id={{$service->id}}',
                dataType: 'json',
                data: {},
                beforeSend: function () {
                },
                success: function (response) {
                    console.log(response);
                    $('#sub-category-selector').html(response.template);
                    $('#category-wise-zone').html(response.template_for_zone);
                    $('#variation-table').html(response.template_for_variant);
                    $('#variation-update-table').html(response.template_for_update_variant);
                },
                complete: function () {
                },
            });
        }

        $(document).ready(function () {
            tinymce.init({
                selector: 'textarea.ckeditor'
            });
        });

        $(".lang_link").on('click', function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang-form").addClass('d-none');
            $(".lang-form2").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            $("#" + lang + "-form2").removeClass('d-none');

            if (lang == '{{$default_lang}}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });



        $(document).ready(function () {
            function calculatePrice() {
                let mrp = parseFloat($('#mrp-price').val()) || 0;
                let discount = parseFloat($('#discount-percent').val()) || 0;

                if (mrp > 0 && discount >= 0) {
                    let price = mrp - (mrp * discount / 100);
                    $('#variant-price').val(price.toFixed(2));
                }
            }

            function calculateDiscount() {
                let mrp = parseFloat($('#mrp-price').val()) || 0;
                let price = parseFloat($('#variant-price').val()) || 0;

                if (mrp > 0 && price >= 0 && price <= mrp) {
                    let discount = ((mrp - price) / mrp) * 100;
                    $('#discount-percent').val(discount.toFixed(2));
                }
            }

            $('#mrp-price, #discount-percent').on('input', function () {
                calculatePrice();
            });

            $('#variant-price').on('input', function () {
                calculateDiscount();
            });
        });


    </script>
@endpush
