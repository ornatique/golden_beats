<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }

    public function getData()
    {
        $categories = Category::latest();

        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                return $row->image
                    ? '<img src="' .asset('uploads/categories/' . $row->image) . '" width="50">'
                    : '-';
            })
            ->addColumn('home', function ($row) {
                return $row->home
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-danger">No</span>';
            })
            ->addColumn('action', function ($row) {

                $html = '';

                // ✏️ EDIT CATEGORY
                if (auth()->user()->can('category-edit')) {
                    $html .= '
            <a href="' . route('admin.categories.edit', $row->id) . '"
               class="btn btn-sm btn-primary mr-1">
                Edit
            </a>
        ';
                }

                // 🗑 DELETE CATEGORY
                if (auth()->user()->can('category-delete')) {
                    $html .= '
            <form method="POST"
                  action="' . route('admin.categories.destroy', $row->id) . '"
                  style="display:inline-block"
                  onsubmit="return confirm(\'Delete this category?\')">
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

            ->rawColumns(['image', 'home', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = public_path('uploads/categories');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            $img = $request->file('image');
            $name = time() . '_' . $img->getClientOriginalName();
            $img->move($path, $name);
            $imagePath =  $name;
        }

        Category::create([
            'name' => $request->name,
            'image' => $imagePath,
            'priority' => $request->priority,
            'home'     => $request->boolean('home'),
            'color' => $request->color,
            'shape' => $request->shape,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // ✅ Validation
        $request->validate([
            'name'     => 'required|string|max:255',
            'priority' => 'required|integer|min:0',
            'shape'    => 'required|string',
            'color'    => 'nullable|string',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif',
        ]);

        // ✅ Prepare update data
        $data = [
            'name'     => $request->name,
            'priority' => $request->priority,
            'shape'    => $request->shape,
            'color'    => $request->color,
            'home'     => $request->boolean('home'), // 🔥 correct usage
        ];

        // ✅ Image upload (only if new image selected)
        if ($request->hasFile('image')) {

            $path = public_path('uploads/categories');

            if (!\File::exists($path)) {
                \File::makeDirectory($path, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($path, $imageName);

            $data['image'] = $imageName;
        }

        // ✅ Update category ONCE
        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted');
    }
}
