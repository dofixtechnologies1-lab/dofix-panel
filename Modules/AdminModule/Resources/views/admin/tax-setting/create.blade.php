@extends('adminmodule::layouts.master')

@section('title', 'Add Tax')

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin-module/plugins/select2/select2.min.css') }}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">Add Tax</h2>
                    </div> 
                    <div class="card">
                        <div class="card-body py-4">
                            <form id="add-new-employee-form" action="{{route('admin.tax-setting.store')}}" method="post" enctype="multipart/form-data">
                                @csrf 
                                    <section> 
                                        <div class="row mb-5">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-md-6 mb-30">
                                                        <div class="input-wrap form-floating form-floating__icon">
                                                            <input type="text" class="form-control" name="tax_name"
                                                                    placeholder="Tax Name"
                                                                    value="{{old('tax_name')}}" required>
                                                            <label>Tax Name</label> 
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-30">
                                                        <div class="input-wrap form-floating form-floating__icon">
                                                            <input type="text" class="form-control" name="sgst"
                                                                    placeholder="SGST"
                                                                    value="{{old('sgst')}}" required>
                                                            <label>SGST</label> 
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-30">
                                                        <div class="input-wrap form-floating form-floating__icon">
                                                            <input type="text" class="form-control" name="cgst"
                                                                    placeholder="CGST"
                                                                    value="{{old('cgst')}}" required>
                                                            <label>CGST</label> 
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-30">
                                                        <div class="input-wrap form-floating form-floating__icon">
                                                            <input type="text" class="form-control" name="igst"
                                                                    placeholder="IGST"
                                                                    value="{{old('igst')}}" required>
                                                            <label>IGST</label> 
                                                        </div>
                                                    </div>
                                                        
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="submit" value="Submit" class="btn btn--primary">
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
@endsection

@push('script')
     

@endpush
