<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use File;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function data()
    {
        $products = Product::with(['category', 'subcategory'])->latest();

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('qr', function ($p) {
                return '
                    <div style="display:flex; align-items:center; gap:24%;">
                        
                        <iframe src="' . route('admin.products.qr', $p->id) . '"
                                width="70"
                                height="70"
                                style="border:none;"></iframe>

                        <a href="' . route('admin.products.qr.pdf', $p->id) . '"
                        target="_blank"
                        class="btn btn-success btn-sm">
                        QR
                        </a>

                    </div>
                ';
            })



            ->addColumn('category', fn($p) => $p->category->name)
            ->addColumn('subcategory', fn($p) => $p->subcategory->name)
            ->addColumn('gallery', function ($p) {
                if (!$p->gallery) return '-';
                return collect($p->gallery)->map(
                    fn($img) =>
                    '<img src="' . asset($img) . '" width="40" class="mr-1">'
                )->implode('');
            })
            ->addColumn('action', function ($p) {
                return '
                    <a href="' . route('admin.products.edit', $p->id) . '" class="btn btn-sm btn-primary">Edit</a>
                     <button class="btn btn-sm btn-danger"
                        onclick="deleteProduct(' . $p->id . ')">
                    Delete
                </button>
                ';
            })
            ->rawColumns(['qr', 'gallery', 'action'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::pluck('name', 'id');
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'gallery.*' => 'image'
        ]);

        $gallery = [];
        if ($request->hasFile('gallery')) {
            $path = public_path('uploads/products');
            if (!File::exists($path)) File::makeDirectory($path, 0755, true);

            foreach ($request->file('gallery') as $img) {
                $name = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move($path, $name);
                $gallery[] = 'uploads/products/' . $name;
            }
        }

        Product::create(array_merge(
            $request->except('gallery'),
            [
                'gallery' => $gallery,
                'label_product' => $request->boolean('label_product'),
                'order_confirm' => $request->boolean('order_confirm'),
            ]
        ));

        return redirect()->route('admin.products.index')->with('success', 'Product created Succesfully');
    }

    public function edit(Product $product)
    {

        $categories = Category::pluck('name', 'id');
        $subcategories = Subcategory::where('category_id', $product->category_id)->pluck('name', 'id');

        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'gallery.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        // 🔹 OLD IMAGES USER KEPT
        $keptImages = $request->old_gallery ?? [];

        // 🔥 OLD IMAGES IN DB
        $existingImages = $product->gallery ?? [];

        // 🔥 FIND REMOVED IMAGES
        $deletedImages = array_diff($existingImages, $keptImages);

        // 🔥 DELETE REMOVED FILES FROM DISK
        foreach ($deletedImages as $img) {
            $path = public_path($img);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // 🔹 HANDLE NEW UPLOADS
        $newImages = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $name);
                $newImages[] = 'uploads/products/' . $name;
            }
        }

        // 🔥 FINAL GALLERY (KEPT + NEW)
        $finalGallery = array_merge($keptImages, $newImages);

        // 🔹 UPDATE PRODUCT
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'number' => $request->number,
            'size' => $request->size,
            'hole_size' => $request->hole_size,
            'gross_weight' => $request->gross_weight,
            'less_weight' => $request->less_weight,
            'weight' => $request->weight,
            'quantity' => $request->quantity,
            'charge' => $request->charge,
            'color' => $request->color,
            'bg_color' => $request->bg_color,
            'label_product' => $request->label_product,
            'gallery' => $finalGallery, // 🔥 array (casted)
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // 🔥 Delete gallery images from disk
        if ($product->gallery && is_array($product->gallery)) {
            foreach ($product->gallery as $img) {
                $path = public_path($img);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        // 🔥 Delete product
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($subcategories);
    }

    public function qrPreview(Product $product)
    {

        $data = json_encode([
            'id'   => $product->id,
            'name' => $product->name,
            'code' => $product->number,
        ]);

        $svg = QrCode::format('svg')
            ->size(70)
            ->margin(1)
            ->generate($data);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }
    public function qrPdf(Product $product)
    {
        $qrData = json_encode([
            'id'   => $product->id,
            'name' => $product->name,
            'code' => $product->number,
        ]);

        // Generate SVG
        $svg = QrCode::format('svg')->size(250)->generate($qrData);

        // Convert SVG to base64
        $qrBase64 = base64_encode($svg);

        $pdf = Pdf::loadView('admin.products.qr-pdf', [
            'product' => $product,
            'qr' => $qrBase64
        ]);

        return $pdf->stream('product-qr-' . $product->id . '.pdf');
    }
}
