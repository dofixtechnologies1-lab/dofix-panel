<?php

namespace Modules\CategoryManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BusinessSettingsModule\Entities\Translation;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\CategoryManagement\Entities\CategoryExtra;
use Modules\CategoryManagement\Entities\Category;

class ExtraController extends Controller
{


    private CategoryExtra $categoryExtra;
    private Category $category;

    use AuthorizesRequests;

    public function __construct(CategoryExtra $categoryExtra,Category $category)
    {
        $this->categoryExtra = $categoryExtra;
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create(Request $request): View|Factory|Application
    {
        $this->authorize('category_view');
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $queryParams = ['search' => $search, 'status' => $status];

        $extras = $this->categoryExtra->with('category')
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })
            ->latest()->paginate(pagination_limit())->appends($queryParams);
       
        $categories = $this->category->where('is_active', 1)->get();

        return view('categorymanagement::admin.category-extras.create', compact('extras','categories','search', 'status'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {   
        $this->authorize('category_add');
        $request->validate([
            'name' => 'required|unique:category_extras',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'status' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ],
            [
                'name.0.required' => translate('default_name_is_required'),
            ]
        );
        
        $extra = $this->categoryExtra;
        $extra->name = $request->name;
        $extra->price = $request->price;
        $extra->status = $request->status;
        $extra->image = file_uploader('category-extra/', 'png', $request->file('image'));
        $extra->status = 1;
        $extra->category_id = $request->category_id;
       
        $extra->save();
    
        Toastr::success("Category Extra Added Successfully");
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return View|Factory|Application|RedirectResponse
     * @throws AuthorizationException
     */
    public function edit(string $id): View|Factory|Application|RedirectResponse
    {   
       
        $this->authorize('category_update');
        $extra = $this->categoryExtra->where('id', $id)->first();
        if (isset($extra)) {

            $categories = $this->category->where('is_active', 1)->get();
            return view('categorymanagement::admin.category-extras.edit', compact('extra','categories'));
        }

        Toastr::error(translate(DEFAULT_204['message']));
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {   
        $this->authorize('category_update');
        $request->validate([
            'name' => 'required|unique:category_extras,name,' .$request->extra_id,
            'price' => 'required|numeric',
            'category_id' => 'required',
        ]);
        
        $id=$request->extra_id;

        $extra = $this->categoryExtra->where('id', $id)->first();
        if (!$extra) {
            return response()->json(response_formatter(CATEGORY_204), 204);
        }
        $extra->name = $request->name;
        if ($request->has('image')) {
            $extra->image = file_uploader('category-extra/', 'png', $request->file('image'), $extra->image);
        }
        $extra->price = $request->price;
        $extra->status = $request->status;
        $extra->category_id = $request->category_id;
        $extra->save();

        Toastr::success(translate(CATEGORY_UPDATE_200['message']));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $this->authorize('category_delete');
        $extra = $this->categoryExtra->where('id', $id)->first();
        if (isset($extra)) {
            file_remover('category-extra/', $extra->image);
            $extra->delete();
            Toastr::success(translate(CATEGORY_DESTROY_200['message']));
            return back();
        }
        Toastr::success(translate(CATEGORY_204['message']));
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function statusUpdate(Request $request, $id): JsonResponse
    {
        $this->authorize('category_manage_status');
        $extra = $this->categoryExtra->where('id', $id)->first();
        $this->categoryExtra->where('id', $id)->update(['is_active' => !$extra->is_active]);

        return response()->json(response_formatter(DEFAULT_STATUS_UPDATE_200), 200);
    }



    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $this->authorize('category_export');
        $items = $this->category->withCount(['children', 'zones'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->latest()->latest()->get();
        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }
}
