<?php

namespace Modules\AdminModule\Http\Controllers\Web\Admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller; 
use Brian2694\Toastr\Facades\Toastr;
use Modules\UserManagement\Entities\Tax;

class TaxSettingController extends Controller
{
     
 
    public function index(Request $request)
    {
        $taxs = Tax::all();

        return view('adminmodule::admin.tax-setting.list', compact('taxs'));
       
    }


    public function create(){

        return view('adminmodule::admin.tax-setting.create');
    }

 
    public function store(Request $request)
    {
         
        $request->validate([
            'tax_name' => 'required',
            'sgst' => 'required',
            'igst' => 'required',
            'cgst' => 'required', 
        ]);


        Tax::create([
            'name' => $request->tax_name,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'igst' => $request->igst, 
        ]);

        Toastr::success(translate(DEFAULT_STORE_200['message']));
        return redirect('/admin/tax-setting/list');

    }

  
    public function edit(string $id)
    {
        $tax = Tax::findOrFail($id);
        
        return view('adminmodule::admin.tax-setting.edit',compact('tax'));

    }

   
    public function update(Request $request, string $id)
    {

        $request->validate([
            'tax_name' => 'required',
            'sgst' => 'required',
            'igst' => 'required',
            'cgst' => 'required', 
        ]);

        $tax = Tax::findOrFail($id);

        $tax->update([
            'name' => $request->tax_name,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'igst' => $request->igst, 
        ]);

        Toastr::success(translate(DEFAULT_UPDATE_200['message']));
        return redirect('/admin/tax-setting/list');
    }
    public function destroy(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();
        Toastr::success(translate(DEFAULT_DELETE_200['message']));
        return back();
         
    }


     

}
