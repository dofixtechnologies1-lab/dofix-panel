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

                    <div class="card shadow-lg rounded border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Enquiry Information</h5>
                        </div>
        
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
        
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body">
                                            <p><strong>Name:</strong> {{ $enquiry->name }}</p>
                                            <p><strong>Mobile Number:</strong> {{ $enquiry->mobile_number }}</p>
                                            <p><strong>Email:</strong> {{ $enquiry->email }}</p>
                                            <p><strong>Service:</strong> {{ $service ? $service->name : 'N/A' }}</p>
                                            <p><strong>Address:</strong> {{ $enquiry->address }}</p>
                                            <p><strong>Message:</strong> {{ $enquiry->message }}</p>
                                        </div>
                                    </div>

                                </div>
        
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body">
                                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($enquiry->date)->format('F d, Y') }}</p>
                                            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($enquiry->time)->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                    
        
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body">
                                            <form action="{{route('admin.booking.enquiryStatus',[$enquiry->id])}}" method="get">
                                                @csrf

                                                <div class="form-group">
                                                    <label for="provider_id" class="font-weight-bold">Assign Provider</label>
                                                    <select name="provider_id" id="provider_id" class="form-control my-2">
                                                        <option value="" selected>Select Provider</option>
                                                        @if (!empty($providersObj))
                                                            @foreach ($providersObj as $providerItem)
                                                                <option value="{{ $providerItem->id}}" {{ $providerItem->id == $enquiry->provider_id ? 'selected' : '' }}>{{$providerItem->full_name ?? ''}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="status" class="font-weight-bold">Update Status</label>
                                                    <select name="status" id="status" class="form-control my-2">
                                                        <option value="0" {{ $enquiry->status == 0 ? 'selected' : '' }}>Pending</option>
                                                        <option value="1" {{ $enquiry->status == 1 ? 'selected' : '' }}>Done</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-success btn-block mt-4">Update Status</button>
                                            </form>
                                        </div>
                                    </div>


                                </div>
                            </div> <!-- End Row -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection