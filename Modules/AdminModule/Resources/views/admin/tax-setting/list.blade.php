@extends('adminmodule::layouts.master')

@section('title', translate('employee_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                        <h2 class="page-title">Tax List</h2>
                        @can('employee_add')
                            <div>
                                <a href="{{route('admin.tax-setting.create')}}" class="btn btn--primary">
                                    <span class="material-icons">add</span>
                                    Add Tax
                                </a>
                            </div>
                        @endcan
                    </div>

                    

                    <div class="card">
                        <div class="card-body"> 
                            <div class="table-responsive">
                                <table id="example" class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th>Tax Name</th>
                                        <th>SGST</th>
                                        <th>CGST</th>
                                        <th>IGST </th>
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($taxs as $key => $tax)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$tax->name}}</td>
                                                <td>{{$tax->sgst}}</td>
                                                <td>{{$tax->cgst}}</td>
                                                <td>{{$tax->igst}}</td>
                                                <td class="text-center"> 

                                                    <a href="{{route('admin.tax-setting.edit',[$tax->id])}}" class="btn btn--primary btn-sm">
                                                        <span class="material-icons">edit</span> {{translate('edit')}}
                                                    </a> 

                                                    
                                                    
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">
                                                    <img class="mb-3 w-150px" src="{{asset('assets/admin-module')}}/images/no-data.png" alt="No data">
                                                    <p class="mb-0">{{translate('no_data_found')}}</p>
                                                </td>
                                        @endforelse
                                      
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    
@endpush
