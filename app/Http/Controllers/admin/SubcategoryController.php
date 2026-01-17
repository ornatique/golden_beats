<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use File;

class SubcategoryController extends Controller
{
    public function index()
    {
        return view('admin.subcategories.index');
    }

    public function getData()
    {
        $subcategories = Subcategory::with('category')->latest();

        return DataTables::of($subcategories)
            ->addIndexColumn()
            ->addColumn('category', fn($row) => $row->category->name)
            ->addColumn('image', function ($row) {
                return $row->image
                    ? '<img src="' . asset('uploads/subcategories/' . $row->image) . '" width="50">'
                    : '-';
            })
            ->addColumn('action', function ($row) {

                $html = '';

                // ✏️ EDIT
                if (auth()->user()->can('subcategory-edit')) {
                    $html .= '
            <a href="' . route('admin.subcategories.edit', $row->id) . '"
               class="btn btn-sm btn-primary mr-1">
                Edit
            </a>
        ';
                }

                // 🗑 DELETE
                if (auth()->user()->can('subcategory-delete')) {
                    $html .= '
            <form method="POST"
                  action="' . route('admin.subcategories.destroy', $row->id) . '"
                  style="display:inline-block"
                  onsubmit="return confirm(\'Are you sure?\')">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-sm btn-danger">
                    Delete
                </button>
            </form>
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

        $categories = Category::pluck('name', 'id');
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required',
            'priority'    => 'required|integer|min:0',
            'image'       => 'nullable|image',
        ]);

        $data = $request->only('category_id', 'name', 'priority', 'color');

        if ($request->hasFile('image')) {
            $path = public_path('uploads/subcategories');
            if (!File::exists($path)) File::makeDirectory($path, 0755, true);
            $img = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move($path, $img);
            $data['image'] =  $img;
        }

        Subcategory::create($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::pluck('name', 'id');
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required',
            'priority'    => 'required|integer|min:0',
            'image'       => 'nullable|image',
        ]);

        $data = $request->only('category_id', 'name', 'priority', 'color');

        if ($request->hasFile('image')) {
            $path = public_path('uploads/subcategories');
            if (!File::exists($path)) File::makeDirectory($path, 0755, true);
            $img = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move($path, $img);
            $data['image'] =  $img;
        }

        $subcategory->update($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory updated');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return back()->with('success', 'Subcategory deleted');
    }
}
