
@if(isset($variants))
    @php
        // Group variants by variant_key to avoid repeated where()->first() calls
        $groupedVariants = $variants->groupBy('variant_key');
    @endphp

    @foreach($groupedVariants as $variantKey => $variantCollection)
        @php
            $variantData = $variantCollection->first(); // Default variant info
        @endphp

        <tr>
            {{-- Variant name --}}
            <th scope="row">
                {{ str_replace('-', ' ', $variantKey) }}
                <input name="variants[]" value="{{ $variantKey }}" class="hide-div">
            </th>

            {{-- Default price --}}
            <td>
                <input type="number"
                       value="{{ optional($variantData)->price ?? 0 }}"
                       class="theme-input-style"
                       id="default-set-{{ $loop->index }}-update"
                       onkeyup="set_update_values('{{ $loop->index }}')"
                       readonly>
            </td>

            {{-- Zone hidden inputs --}}
            @foreach($zones as $zone)
                @php
                    $zoneData = $variantCollection->where('zone_id', $zone->id)->first();
                @endphp

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_price"
                       value="{{ optional($zoneData)->price ?? 0 }}"
                       class="theme-input-style default-get-{{ $loop->parent->index }}-update">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_mrp_price"
                       value="{{ optional($zoneData)->mrp_price ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_discount_percent"
                       value="{{ optional($zoneData)->discount ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_convenience_fee"
                       value="{{ optional($zoneData)->convenience_fee ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_convenience_gst"
                       value="{{ optional($zoneData)->convenience_gst ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_aggregator_fee"
                       value="{{ optional($zoneData)->aggregator_fee ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_aggregator_gst"
                       value="{{ optional($zoneData)->aggregator_gst ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_var_description"
                       value="{{ optional($zoneData)->var_description ?? '' }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_var_duration"
                       value="{{ optional($zoneData)->var_duration ?? '' }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_duration_hour"
                       value="{{ optional($zoneData)->duration_hour ?? 0 }}" class="hide-div">

                <input type="hidden" name="{{ $variantKey }}_{{ $zone->id }}_duration_minute"
                       value="{{ optional($zoneData)->duration_minute ?? 0 }}" class="hide-div">
            @endforeach

            {{-- Cover image --}}
            <td>
                @if(optional($variantData)->cover_image)
                    <img src="{{ asset('storage/service/variant/' . optional($variantData)->cover_image) }}"
                         width="60" height="60" style="object-fit:cover;border-radius:6px;">
                         <input type="hidden" name="old_var_image[{{ $variantData->variant_key }}]" value="{{ $variantData->cover_image }}">
                @else
                    <span class="text-muted">No Image</span>
                @endif
            </td>

            {{-- Action buttons --}}
            <td>
                <a class="btn btn-sm btn--danger service-ajax-remove-variant"
                   data-route="{{ route('admin.service.ajax-delete-db-variant', [$variantKey, optional($variantData)->service_id]) }}"
                   data-id="variation-update-table">
                    <span class="material-icons m-0">delete</span>
                </a>

                <a class="btn btn-sm btn--primary service-ajax-edit-variant"
                   href="javascript:void(0);"
                   data-bs-toggle="modal"
                   data-bs-target="#editVariantModal"
                   data-route="{{ route('admin.service.ajax-db-variant', [$variantKey, optional($variantData)->id]) }}"
                   data-id="variation-update-table"
                   title="Edit">
                    <span class="material-icons m-0">edit</span>
                </a>
            </td>
        </tr>
    @endforeach
@endif

@push('script')
<script>

    "use strict";
    document.addEventListener('DOMContentLoaded', function () {
        var elements = document.querySelectorAll('.service-ajax-remove-variant');
        elements.forEach(function (element) {
            element.addEventListener('click', function () {
                var route = this.getAttribute('data-route');
                var id = this.getAttribute('data-id');
                ajax_remove_variant(route, id);
            });
        });

        function set_update_values(key) {
            alert(key);
            var updateElements = document.querySelectorAll('.default-get-' + key + '-update');
            var setValue = document.getElementById('default-set-' + key + '-update').value;
            updateElements.forEach(function (element) {
                element.value = setValue;
            });
        }
    });

     $(document).on('click', '.service-ajax-edit-variant', function () {
        const route = $(this).data('route');

        $('#edit-variant-modal-content').html('Loading...');

        $.ajax({
            url: route,
            type: 'GET',
            success: function (response) {
                $('#edit-variant-modal-content').html(response.template);
            },
            error: function () {
                $('#edit-variant-modal-content').html('<div class="alert alert-danger">Failed to load content.</div>');
            }
        });
    });
</script>
@endpush
