<form method="POST" action="{{ route('admin.service.update-modal-variant') }}" class="row g-3">
    @csrf
    <input type="hidden" name="service_id" value="{{ $variants->service_id ?? '' }}">
    <input type="hidden" name="variant_key" value="{{ $variants->variant_key ?? '' }}">

    <div class="col-md-6 form-floating mb-3">
        <input type="text" class="form-control" name="variant" placeholder="Variant Name *"
               value="{{ $variants->variant ?? 0 }}">
        <label>Variant Name *</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="number" class="form-control" name="mrp_price" placeholder="MRP Price *"
               value="{{ $variants->mrp_price ?? 0 }}">
        <label>MRP Price *</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="number" class="form-control" name="discount" placeholder="Discount Percent *"
               value="{{ $variants->discount ?? 0 }}">
        <label>Discount Percent %*</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="number" class="form-control" name="variant_price" placeholder="{{translate('price')}} *"
               value="{{ $variants->price ?? 0 }}">
        <label>{{translate('price')}} *</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="number" class="form-control" name="convenience_fee" placeholder="Convenience Fee *"
               value="{{ $variants->convenience_fee ?? 0 }}">
        <label>Convenience Fee *</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        {{-- <input type="number" class="form-control" name="convenience_gst" placeholder="Convenience GST *" value="{{ $variants->convenience_gst ?? 0 }}" >
        <label>Convenience GST % *</label> --}}
        <select class="js-select theme-input-style w-100 form-error-wrap" name="convenience_gst" id="convenience_gst">
            <option value="0" selected disabled>Select Tax *</option>
            @foreach($taxes as $tax)
                <option
                    value="{{$tax->id}}" {{ ($variants->convenience_gst == $tax->id)?'selected':'' }}>{{$tax->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="number" class="form-control" name="aggregator_fee" placeholder="Aggregator Fee *"
               value="{{ $variants->aggregator_fee ?? 0 }}">
        <label>Aggregator Fee *</label>
    </div>
    <div class="col-md-6 form-floating mb-3">
        {{-- <input type="number" class="form-control" name="aggregator_gst" placeholder="Aggregator GST *" value="{{ $variants->aggregator_gst ?? 0 }}" >
        <label>Aggregator GST %*</label> --}}
        <select class="js-select theme-input-style w-100 form-error-wrap" name="aggregator_gst" id="aggregator_gst">
            <option value="0" selected disabled>Select Tax *</option>
            @foreach($taxes as $tax)
                <option
                    value="{{$tax->id}}" {{ ($variants->aggregator_gst == $tax->id)?'selected':'' }}>{{$tax->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-floating mb-3">
        <input type="text" class="form-control" name="var_description" placeholder="Description *"
               value="{{ $variants->var_description ?? '' }}">
        <label>Description</label>
    </div>
    {{-- <div class="col-md-6 form-floating mb-3">
        <input type="time" class="form-control" name="var_duration" placeholder="Duration (in hour minutes) *" value="{{ $variants->var_duration ?? '' }}" >
        <label>Duration (in hour minutes) *</label>
    </div> --}}
    <div class="col-md-3 form-floating mb-3">
        <input type="number" min="0" class="form-control" name="duration_hour" placeholder="Duration Hour *"
               value="{{ $variants->duration_hour ?? 0 }}">
        <label>Duration Hour *</label>
    </div>
    <div class="col-md-3 form-floating mb-3">
        <input type="number" min="0" max="59" class="form-control" name="duration_minute"
               placeholder="Duration Minute *" value="{{ $variants->duration_minute ?? 0 }}">
        <label>Duration Minute *</label>
    </div>
    <div class="col-md-6">
        <input type="submit" class="form-control btn btn-primary" value="Update">
    </div>
</form>
