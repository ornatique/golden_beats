<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Container\Attributes\Log;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use App\Models\Category;

class UserController extends Controller
{
    // public function index(Request $request)
    // {
    //     $authUser = auth()->user();

    //     // 🚫 Customer must NOT use filters
    //     if ($authUser->role === 'customer' && ($request->filled('only') || $request->filled('status'))) {
    //         return redirect()->route('admin.dashboard')
    //             ->with('error', 'Unauthorized access');
    //     }

    //     // 🚫 Invalid filter values
    //     $allowedOnly = ['customer', 'user'];
    //     $allowedStatus = ['active', 'inactive'];

    //     if ($request->filled('only') && !in_array($request->only, $allowedOnly)) {
    //         return redirect()->route('admin.dashboard');
    //     }

    //     if ($request->filled('status') && !in_array($request->status, $allowedStatus)) {
    //         return redirect()->route('admin.dashboard');
    //     }

    //     return view('admin.users.index');
    // }
    public function index(Request $request)
    {
        $only   = $request->get('only', 'user');   // default = user
        $status = $request->get('status');

        // ✅ Allowed values (security)
        $allowedOnly   = ['user', 'customer'];
        $allowedStatus = ['active', 'inactive'];

        // 🚫 Invalid query values
        if (!in_array($only, $allowedOnly)) {
            abort(404);
        }

        if ($status && !in_array($status, $allowedStatus)) {
            abort(404);
        }

        // 🔐 PERMISSION ENFORCEMENT (KEY PART)
        if ($only === 'user' && !auth()->user()->can('user-view')) {
            abort(403, 'You are not allowed to view users');
        }

        if ($only === 'customer' && !auth()->user()->can('customer-view')) {
            abort(403, 'You are not allowed to view customers');
        }

        // ✅ Build query
        $query = User::query();

        if ($only === 'customer') {
            $query->where('type', 'customer');

            if ($status) {
                $query->where('status', $status);
            }
        } else {
            $query->where('type', 'user');
        }

        return view('admin.users.index', [
            'only'   => $only,
            'status' => $status,
        ]);
    }


    private function userListQuery(Request $request)
    {
        //dd($request->only);
        $query = User::with('roles')->orderBy('id', 'desc');

        // $authUser = auth()->user();

        // Customer login → only own record
        // if ($authUser->role === 'customer') {
        //     $query->where('id', $authUser->id);
        // }

        // URL: ?only=customer
        if ($request->only === 'customer') {
            $query->where('role', 'customer');
        }
        if ($request->only === 'user') {
            $query->where('role', '!=', 'customer');
        }
        // URL: ?status=active
        if ($request->status === 'active') {
            $query->where('status', 1);
        }

        if ($request->status === 'inactive') {
            $query->where('status', 0);
        }

        return $query;
    }

    public function show()
    {
        abort(404);
    }
    // DataTables AJAX
    public function getData(Request $request)
    {
        $users = $this->userListQuery($request);
        $only = $request->get('only');
        return DataTables::of($users)

            ->addColumn('profile_image', function ($user) {
                if ($user->image) {
                    return '<img src="' . asset('uploads/users/' . $user->image) . '"
                 width="80"
                 height="80"
                 style="object-fit:cover;border-radius:50%;">';
                }
                return '<span class="badge bg-secondary">No Image</span>';
            })

            ->addColumn('contact', function ($user) {
                return $user->number ?? '-';
            })

            ->addColumn('reg_date', function ($user) {
                return $user->created_at
                    ? $user->created_at->format('d-m-Y H:i')
                    : '-';
            })

            ->addColumn('role', function ($user) {
                return ucfirst($user->role);
            })

            ->addColumn('status', function ($user) {

                $checked  = $user->status ? 'checked' : '';
                $disabled = $user->role === 'admin' ? 'disabled' : '';

                return '
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                            class="custom-control-input toggle-status"
                            id="statusSwitch' . $user->id . '"
                            data-id="' . $user->id . '"
                            ' . $checked . ' ' . $disabled . '>
                        <label class="custom-control-label"
                            for="statusSwitch' . $user->id . '"></label>
                    </div>
                ';
            })

            ->addColumn('action', function ($user) use ($only) {

                if (auth()->user()->role === 'customer') {
                    return '<span class="badge bg-secondary">Not Allowed</span>';
                }

                if ($user->hasRole('admin')) {
                    return '<span class="badge bg-secondary">Not Allowed</span>';
                }

                $edit = '<a href="' . route('admin.users.edit', [
                    'user' => $user->id,
                    'only' => $only
                ]) . '" class="btn btn-sm btn-primary">Edit</a>';


                $delete = '<form method="POST"
                            action="' . route('admin.users.destroy', $user->id) . '"
                            style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm(\'Are you sure?\')">
                                    Delete
                            </button>
                        </form>';

                return $edit . ' ' . $delete;
            })

            ->rawColumns(['profile_image', 'status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }



    public function create()
    {
        $roles = Role::all();
        $categories = Category::pluck('name', 'id');
        return view('admin.users.create', compact('roles', 'categories'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'number' => 'required',
            'state' => 'required',
            'city' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp',

        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {

            $image = $request->file('image');

            // 🔹 Folder path
            $destinationPath = public_path('uploads/users');

            // 🔹 Create folder if NOT exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // 🔹 Unique image name
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // 🔹 Upload image
            $image->move($destinationPath, $imageName);

            // 🔹 Save path for DB
            $imagePath = 'uploads/users/' . $imageName;
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'number' => $request->number,
            'state' => $request->states,
            'city' => $request->city,
            'image' => $imageName,
            'category_ids' => json_encode($request->category_ids),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to edit admin user');
        }
        $selectedCategories = json_decode($user->category_ids, true) ?? [];
        $roles = Role::all();
        $categories = Category::pluck('name', 'id');
        return view('admin.users.edit', compact('user', 'roles', 'categories', 'selectedCategories'));
    }

    public function update(Request $request, User $user)
    {

        //  Prevent non-admin from editing admin
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to update admin user');
        }

        // ✅ Validation (EDIT MODE)
        $request->validate([
            'name'   => 'required',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'role'   => 'required',
            'number' => 'required',
            'state'  => 'required',
            'city'   => 'required',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        // 🔹 Update basic fields
        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
            'number' => $request->number,
            'state'  => $request->state,
            'city'   => $request->city,
        ];

        // 🔑 Password (ONLY if entered)
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // 🖼 Image (ONLY if uploaded)
        if ($request->hasFile('image')) {

            $destinationPath = public_path('uploads/users');

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);

            $data['image'] = $imageName;
        }

        // 🏷 Category (ONLY if customer)
        if ($request->role === 'customer') {
            $data['category_ids'] = json_encode($request->category_ids);
        } else {
            $data['category_ids'] = null;
        }

        // 🔄 Update user
        $user->update($data);

        // 🔄 Sync role
        $user->syncRoles([$request->role]);

        return redirect()
            ->route('admin.users.index', [
                'only' => request('only')
            ])
            ->with('success', 'User updated successfully');
    }


    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Admin user cannot be deleted');
        }
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'exists' => true
            ]);
        }
    }

    public function updateStatus(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:users,id',
            'status' => 'required|boolean',
        ]);

        // Optional: permission check
        if (!auth()->user()->can('user-edit')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($request->id);
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin user cannot be deactivated'
            ], 403);
        }
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
