<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerAd;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;


class BannerAdController extends Controller
{
    public function index()
    {
        return view('admin.banner_ads.index');
    }

    public function data()
    {
        $ads = BannerAd::with(['category', 'subcategory', 'product'])->latest();

        return DataTables::of($ads)
            ->addIndexColumn()

            // CATEGORY NAME
            ->addColumn('category', function ($ad) {
                return $ad->category->name ?? '-';
            })

            // SUBCATEGORY NAME
            ->addColumn('subcategory', function ($ad) {
                return $ad->subcategory->name ?? '-';
            })

            // PRODUCT NAME
            ->addColumn('product', function ($ad) {
                return $ad->product->name ?? '-';
            })

            // IMAGE
            ->addColumn('image', function ($ad) {
                return $ad->image
                    ? '<img src="' . asset($ad->image) . '" width="100" class="img-thumbnail">'
                    : '-';
            })

            // ACTIONS
            ->addColumn('action', function ($ad) {

                $html = '';

                // ✏️ EDIT (banner-ad-edit)
                if (auth()->user()->can('banner-ad-edit')) {
                                $html .= '
                        <a href="' . route('admin.banner-ads.edit', $ad->id) . '"
                        class="btn btn-primary btn-sm mr-1">
                        Edit
                        </a>
                    ';
                            }

                            // 🗑 DELETE (banner-ad-delete)
                            if (auth()->user()->can('banner-ad-delete')) {
                                $html .= '
                        <button class="btn btn-danger btn-sm"
                                onclick="deleteBannerAd(' . $ad->id . ')">
                        Delete
                        </button>
                    ';
                }

                return $html ?: '-';
            })
            ->rawColumns(['action'])


            ->rawColumns(['image', 'action'])
            ->make(true);
    }


    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.banner_ads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'     => 'nullable|integer',
            'category_id'    => 'nullable|integer',
            'subcategory_id' => 'nullable|integer',
            'image'          => 'required|image',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banner_ads'), $name);
            $data['image'] = 'uploads/banner_ads/' . $name;
        }

        BannerAd::create($data);

        return redirect()
            ->route('admin.banner-ads.index')
            ->with('success', 'Banner Ad created');
    }

    public function edit(BannerAd $bannerAd)
    {
        $categories = Category::orderBy('name')->get();

        $subcategories = Subcategory::where('category_id', $bannerAd->category_id)->get();

        $products = Product::where('subcategory_id', $bannerAd->subcategory_id)->get();

        return view(
            'admin.banner_ads.edit',
            compact('bannerAd', 'categories', 'subcategories', 'products')
        );
    }


    public function update(Request $request, BannerAd $bannerAd)
    {
        $data = $request->validate([
            'product_id'     => 'nullable|integer',
            'category_id'    => 'nullable|integer',
            'subcategory_id' => 'nullable|integer',
            'image'          => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($bannerAd->image && file_exists(public_path($bannerAd->image))) {
                unlink(public_path($bannerAd->image));
            }

            $file = $request->file('image');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banner_ads'), $name);
            $data['image'] = 'uploads/banner_ads/' . $name;
        }

        $bannerAd->update($data);

        return redirect()
            ->route('admin.banner-ads.index')
            ->with('success', 'Banner Ad updated');
    }

    public function getSubcategories($categoryId)
    {
        return Subcategory::where('category_id', $categoryId)
            ->select('id', 'name')
            ->get();
    }

    public function destroy(BannerAd $bannerAd)
    {
        $bannerAd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner Ad deleted successfully'
        ]);
    }


    public function getProducts($subcategoryId)
    {
        return Product::where('subcategory_id', $subcategoryId)
            ->select('id', 'name')
            ->get();
    }
}
