@extends('adminmodule::layouts.master')

@section('title',translate('service_setup'))

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
                        <h2 class="page-title">{{translate('add_new_service')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <div>
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
                                <form action="{{route('admin.service.store')}}" method="post" enctype="multipart/form-data"
                                    id="form-wizard">
                                    @csrf

                                    <h3>{{translate('service_information')}}</h3>
                                    <section>
                                        <div class="row">
                                            <div class="col-lg-5 mb-5 mb-lg-0">
                                                @if($language)
                                                    <div class="mb-30">
                                                        <div class="form-floating form-floating__icon lang-form" id="default-form">
                                                            <input type="text" name="name[]"
                                                                class="form-control default-name" required
                                                                placeholder="{{translate('service_name')}}">
                                                            <label>{{translate('service_name')}} ({{ translate('default') }}
                                                                )</label>
                                                            <span class="material-icons">subtitles</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                    @foreach ($language?->live_values as $lang)
                                                        <div class="mb-30">
                                                            <div class="form-floating form-floating__icon d-none lang-form"
                                                                id="{{$lang['code']}}-form">
                                                                <input type="text" name="name[]"
                                                                    class="form-control input-language"
                                                                    placeholder="{{translate('service_name')}}">
                                                                <label>{{translate('service_name')}}
                                                                    ({{strtoupper($lang['code'])}})</label>
                                                                <span class="material-icons">subtitles</span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                                    @endforeach
                                                @else
                                                    <div class="lang-form">
                                                        <div class="mb-30">
                                                            <div class="form-floating form-floating__icon">
                                                                <input type="text" class="form-control" name="name[]"
                                                                       placeholder="{{translate('service_name')}} *"
                                                                       required>
                                                                <label>{{translate('service_name')}} *</label>
                                                                <span class="material-icons">subtitles</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                @endif
                                                <div class="mb-30">
                                                    <div class="form-error-wrap">
                                                        <select class="js-select theme-input-style w-100 form-error-wrap" name="category_id" id="category-id">
                                                            <option value="0" selected
                                                                    disabled>{{translate('choose_Category')}} *
                                                            </option>
                                                            @foreach($categories as $category)
                                                                <option
                                                                        value="{{$category->id}}">{{$category->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-30 form-error-wrap" id="sub-category-selector">
                                                    <div class="form-error-wrap">
                                                        <select class="subcategory-select theme-input-style w-100"
                                                                name="sub_category_id" id="sub-category-id">
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating form-floating__icon">
                                                        <input type="hidden" class="form-control" name="tax" min="0"
                                                               max="100" step="any"
                                                               placeholder="{{translate('add_tax_percentage')}} *"
                                                               required="" value="0">
                                                        {{-- <label>{{translate('add_tax_percentage')}} *</label>
                                                        <span class="material-icons">percent</span> --}}
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating form-floating__icon">
                                                        <input type="hidden" class="form-control"
                                                               name="min_bidding_price" min="0" step="any"
                                                               placeholder="Service Starting Price *"
                                                               required="" value="0">
                                                        {{-- <label>Service Starting Price *</label> --}}
                                                        {{-- <span class="material-icons">price_change</span> --}}
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control w-100" name="tags"
                                                               placeholder="{{translate('Enter_tags')}}"
                                                               data-role="tagsinput">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-5 mb-5 mb-sm-0">
                                                <div class="d-flex flex-column align-items-center gap-3">
                                                    <p class="mb-0">{{translate('thumbnail_image')}} *</p>
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="upload-file form-error-wrap">
                                                            <input type="file" class="upload-file__input"
                                                                   name="thumbnail" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                            <div class="upload-file__img">
                                                                <img src="{{asset('assets/admin-module/img/media/upload-file.png')}}"
                                                                        alt="{{ translate('service') }}">
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
                                                    <p class="mb-0">{{translate('cover_image')}} *</p>
                                                    <div>
                                                        <div class="upload-file form-error-wrap">
                                                            <input type="file" class="upload-file__input"
                                                                   name="cover_image" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                            <div class="upload-file__img upload-file__img_banner">
                                                                <img src="{{asset('assets/admin-module/img/media/banner-upload-file.png')}}"
                                                                     alt="{{ translate('service-cover-image') }}">
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
                                                                <textarea type="text" class="form-control" name="short_description[]" required></textarea>
                                                                <label>{{translate('short_description')}}
                                                                    ({{translate('default')}}) *</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4 mt-md-5">
                                                        <div class="form-error-wrap">
                                                            <label for="editor"
                                                                class="mb-2">{{translate('long_Description')}}
                                                                ({{translate('default')}})
                                                                <span class="text-danger">*</span></label>
                                                            <section id="editor" class="dark-support">
                                                            <textarea class="ckeditor" name="description[]" required></textarea>
                                                            </section>
                                                        </div>
                                                    </div>
                                                </div>
                                                @foreach ($language?->live_values as $lang)
                                                    <div class="d-none lang-form2" id="{{$lang['code']}}-form2">
                                                        <div class="col-lg-12 mt-5">
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <textarea type="text" class="form-control" name="short_description[]"></textarea>
                                                                    <label>{{translate('short_description')}}
                                                                        ({{strtoupper($lang['code'])}}) *</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-4 mt-md-5">
                                                            <div class="form-error-wrap">
                                                                <label for="editor" class="mb-2">{{translate('long_Description')}}({{strtoupper($lang['code'])}})
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <section id="editor" class="dark-support">
                                                                <textarea class="ckeditor" name="description[]"></textarea>
                                                                </section>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="normal-form">
                                                    <div class="col-lg-12 mt-5">
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <textarea type="text" class="form-control" name="short_description[]" required></textarea>
                                                                <label>{{translate('short_description')}} *</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4 mt-md-5">
                                                        <div class="form-error-wrap">
                                                            <label for="editor" class="mb-2">{{translate('long_Description')}}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <section id="editor" class="dark-support">
                                                                <textarea class="ckeditor" name="description[]" required></textarea>
                                                            </section>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </section>

                                    <h3>{{translate('price_variation')}}</h3>
                                    <section>
                                        <div class="d-flex flex-wrap mb-3 row">
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="text" class="form-control" name="variant_name"
                                                       id="variant-name"
                                                       placeholder="{{translate('add_variant')}} *">
                                                <label>{{translate('add_variant')}} *</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="number" class="form-control" name="mrp_price"
                                                       id="mrp-price"
                                                       placeholder="MRP Price *" value="0">
                                                <label>MRP Price *</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="number" class="form-control" name="discount"
                                                       id="discount-percent"
                                                       placeholder="Discount Percent *" value="0">
                                                <label>Discount Percent %*</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="number" class="form-control" name="variant_price"
                                                       id="variant-price"
                                                       placeholder="{{translate('price')}} *" value="0">
                                                <label>{{translate('price')}} *</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="number" class="form-control" name="convenience_fee"
                                                       id="convenience-fee"
                                                       placeholder="Convenience Fee *" value="0">
                                                <label>Convenience Fee *</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                {{-- <input type="number" class="form-control" name="convenience_gst"
                                                       id="convenience-gst"
                                                       placeholder="Convenience GST *" value="0">
                                                <label>Convenience GST % *</label> --}}
                                                <select class="js-select theme-input-style w-100 form-error-wrap" id="convenience-gst" name="convenience_gst"  >
                                                    <option value="0" selected disabled>Select Tax *
                                                    </option>
                                                    @foreach($taxes as $tax)
                                                        <option
                                                                value="{{$tax->id}}">{{$tax->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
                                                <input type="number" class="form-control" name="aggregator_fee"
                                                       id="aggregator-fee"
                                                       placeholder="Aggregator Fee *" value="0">
                                                <label>Aggregator Fee *</label>
                                            </div>
                                            <div class="col-lg-4 col-md-6 form-floating mb-3">
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
                                            
                                            <div class="col-lg-6 col-md-6 mb-3">
                                            <button type="button" class="btn btn--primary" id="service-ajax-variation">
                                                <span class="material-icons">add</span>
                                                {{translate('add')}}
                                            </button>
                                            </div>
                                             
                                        </div>

                                        <div class="table-responsive p-01">
                                            <table class="table align-middle table-variation">
                                                <thead id="category-wise-zone" class="text-nowrap">
                                                    @include('servicemanagement::admin.partials._category-wise-zone',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])
                                                </thead>
                                                <tbody id="variation-table">
                                                    @include('servicemanagement::admin.partials._variant-data',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])
                                                </tbody>
                                            </table>
                                        </div>
                                    </section>
                                </form>
                            </div>
                        </div>
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
    <script src="{{asset('assets/provider-module')}}/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="{{asset('assets/admin-module/plugins/tinymce/tinymce.min.js')}}"></script>
    <script src="{{asset('assets/ckeditor/jquery.js')}}"></script>

    <script>
        (function ($) {
            "use strict";

            let formWizard = $("#form-wizard");

            $('body').on('click', function (event) {
                if (!$(event.target).closest('#editor').length) {
                    if($("#editor iframe").contents().find("body").text() !== ""){
                        formWizard.find('.desc-err').remove();
                    };
                }

                if (!$(event.target).closest('[name=category_id], [name=category_id] + .select2').length) {
                    if($('[name=category_id]').val()) {
                        $('[name=category_id]').parents('.form-error-wrap').siblings('[for="category-id"]').remove();
                    }
                }
            });



            // Form validation with jQuery
            formWizard.validate({
                errorPlacement: function (error, element) {
                    element.parents('.form-floating, .form-error-wrap').after(error);
                },
                rules: {
                    "name[]": "required",
                    category_id: "required",
                    tax: "required",
                    min_bidding_price: "required",
                    "short_description[]": "required",
                    thumbnail: "required",
                    cover_image: "required",
                    "description[]": "required",
                },
                messages: {
                    "name[]": "Please enter name",
                    category_id: "Please enter category id",
                    tax: "Please enter Tax",
                    min_bidding_price: "Please enter min bidding price",
                    "short_description[]": "Please enter short description",
                    thumbnail: "Please enter thumbnail",
                    cover_image: "Please upload cover image",
                    "description[]": "Please enter description",
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
                    next: "Next",
                    previous: "Previous"
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (newIndex < currentIndex) {
                        return true;
                    }

                    if (currentIndex == 0) {
                        var errorMessageElement = formWizard.find(".desc-err");

                        if($("#editor iframe").contents().find("body").text() == ""){
                            if (errorMessageElement.length > 0) {
                                errorMessageElement.text("Please Add Description");
                            } else {
                                formWizard.find("#editor").after(`<span class="text-danger desc-err mt-2">Please Add Description</span>`)
                                return false;
                            }
                        } else {
                            formWizard.find('.desc-err').remove();
                        };
                    }

                    formWizard.validate().settings.ignore = ":disabled,:hidden";
                    return formWizard.valid();
                },
                onFinished: function (event, currentIndex) {
                    event.preventDefault();
                    let hasRows = $("#variation-table > tr").length > 0;
                    if (hasRows) {
                        formWizard.submit();
                    } else {
                        var errorMessageElement = formWizard.find(".table-row-err");

                        if (errorMessageElement.length > 0) {
                            errorMessageElement.text("Please Add Variation");
                        } else {
                            formWizard.find("#variation-table").parents(".table-responsive").after(`<span class="text-danger table-row-err">Please Add Variation</span>`);
                        }
                    }
                }
            });

        })(jQuery);
    </script>


    {{-- <script>
        (function ($) {
            "use strict";

            let formWizard = $("#form-wizard");

            // Form validation with jQuery
            formWizard.validate({
                errorPlacement: function (error, element) {
                    element.parents('.form-floating, .form-error-wrap').after(error);
                },
            });

            function setValidationRulesAndMessages(rules, messages) {
                formWizard.validate().settings.rules = rules;
                formWizard.validate().settings.messages = messages;
            }

            function handleTableRowValidation() {
                let hasRows = $("#variation-table > tr").length > 0;
                if (!hasRows) {
                    var errorMessageElement = formWizard.find(".table-row-err");

                    if (errorMessageElement.length > 0) {
                        errorMessageElement.text("Please Add Variation");
                    } else {
                        formWizard.find("#variation-table").parents(".table-responsive").after(`<span class="text-danger table-row-err">Please Add Variation</span>`);
                    }
                    return false;
                } else {
                    formWizard.find(".table-row-err").remove();
                }
                return true;
            }

            formWizard.steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "fade",
                stepsOrientation: "vertical",
                autoFocus: true,
                labels: {
                    finish: "Submit",
                    next: "Next",
                    previous: "Previous"
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (newIndex < currentIndex) {
                        return true;
                    }

                    switch (currentIndex) {
                        case 0:
                            setValidationRulesAndMessages({
                                "name[]": "required",
                                category_id: "required",
                                tax: "required",
                                min_bidding_price: "required",
                                "short_description[]": "required",
                                thumbnail: "required",
                                cover_image: "required",
                            }, {
                                "name[]": "Please enter name",
                                category_id: "Please enter category id",
                                tax: "Please enter Tax",
                                min_bidding_price: "Please enter min bidding price",
                                "short_description[]": "Please enter short description",
                                thumbnail: "Please enter thumbnail",
                                cover_image: "Please upload cover image",
                            });
                            break;
                        case 1:
                            if (!handleTableRowValidation()) {
                                return false;
                            break;
                    }

                    formWizard.validate().settings.ignore = ":disabled,:hidden";
                    return formWizard.valid();
                },
                onFinished: function (event, currentIndex) {
                    event.preventDefault();
                    let hasRows = $("#variation-table > tr").length > 0;
                    if(hasRows) {
                        formWizard.submit();
                    } else {
                        console.log("Please Add Variation");
                    }
                }
            });

        })(jQuery);
    </script> --}}

    <script>
        "use strict";

        $(document).ready(function () {
            $('.js-select').select2();
            $('.subcategory-select').select2({
                placeholder: "Choose Subcategory"
            });
        });

        var variationCount = $("#variation-table > tbody > tr").length;

        // $("#form-wizard").steps({
        //     headerTag: "h3",
        //     bodyTag: "section",
        //     transitionEffect: "slideLeft",
        //     autoFocus: true,
        //     onFinished: function (event, currentIndex) {
        //         //validation
        //         let category = $("#category-id").val();
        //         if (category === '0') {
        //             toastr.error("{{translate('Select_valid_category')}} *");
        //         }

        //         if (variationCount > 0) {
        //             $("#service-add-form")[0].submit();
        //         } else {
        //             $('#service-add-form').trigger('focus')
        //             toastr.error("{{translate('Must_add_a_service_variation')}}");
        //         }
        //     }
        // });

        $("#service-ajax-variation").on('click', function (){
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

            if (name.length > 0 && price >= 0 && mrpPrice >= 0 && discount >= 0 && convenienceFee >= 0 && aggregatorFee >= 0 ) {
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
                        variationCount++;
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

        document.querySelectorAll('.service-ajax-remove-variant').forEach(function(element) {
            element.addEventListener('click', function() {
                var route = this.getAttribute('data-route');
                var id = this.getAttribute('data-id');
                ajax_remove_variant(route, id);
            });
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
                            $('#' + id).html(response.template);
                        },
                        complete: function () {
                        },
                    });
                }
            })
        }

        $("#category-id").change(function (){
            let id = this.value;
            let route = "{{ url('/admin/category/ajax-childes/') }}/" + id;
            ajax_switch_category(route)
        });

        function ajax_switch_category(route) {
            $.get({
                url: route,
                dataType: 'json',
                data: {},
                beforeSend: function () {
                },
                success: function (response) {
                    $('#sub-category-selector').html(response.template);
                    $('#category-wise-zone').html(response.template_for_zone);
                    $('#variation-table').html(response.template_for_variant);
                },
                complete: function () {
                },
            });
        }

        $(".lang_link").on('click', function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang-form").addClass('d-none');
            $(".lang-form2").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            $("#" + lang + "-form").removeClass('d-none');
            $("#" + lang + "-form2").removeClass('d-none');

        });

        $(document).ready(function () {
            tinymce.init({
                selector: 'textarea.ckeditor'
            });
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
