
@if(isset($variants))
    @php($variant_keys = $variants->pluck('variant_key')->unique()->toArray())
    @foreach($variant_keys as $key=>$item)
        <tr>
            <th scope="row">
                {{str_replace('-',' ',$item)}}
                <input name="variants[]" value="{{$item}}" class="hide-div">
            </th>
            <td>
                <input type="number"
                       value="{{$variants->where('price','>',0)->where('variant_key',$item)->first()->price??0}}"
                       class="theme-input-style" id="default-set-{{$key}}-update"
                       onkeyup="set_update_values('{{$key}}')" readonly>
            </td>
            @foreach($zones as $zone)
                {{-- <td> --}}
                <input type="hidden" name="{{$item}}_{{$zone->id}}_price" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->price??0}}" class="theme-input-style default-get-{{$key}}-update">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_mrp_price" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->mrp_price??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_discount_percent" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->discount??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_convenience_fee" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->convenience_fee??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_convenience_gst" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->convenience_gst??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_aggregator_fee" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->aggregator_fee??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_aggregator_gst" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->aggregator_gst??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_var_description" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->var_description??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_var_duration" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->var_duration??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_duration_hour" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->duration_hour??0}}" class="hide-div">
                <input type="hidden" name="{{$item}}_{{$zone->id}}_duration_minute" value="{{$variants->where('zone_id',$zone->id)->where('variant_key',$item)->first()->duration_minute??0}}" class="hide-div">


                {{-- </td> --}}
            @endforeach
            <td>
                <a class="btn btn-sm btn--danger service-ajax-remove-variant"
                   data-route="{{ route('admin.service.ajax-delete-db-variant',[$item,$variants->where('variant_key',$item)->first()->service_id]) }}"
                   data-id="variation-update-table">
                    <span class="material-icons m-0">delete</span>
                </a>
                <a class="btn btn-sm btn--primary service-ajax-edit-variant"
                href="javascript:void(0);"
                data-bs-toggle="modal"
                data-bs-target="#editVariantModal"
                data-route="{{ route('admin.service.ajax-db-variant', [$item, $variants->where('variant_key',$item)->first()->id]) }}"
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
