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
class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }
    public function show()
    {
        abort(404);
    }
    // DataTables AJAX
    public function getData()
    {

        $users = User::with('roles')->orderBy('id', 'desc');;

        return DataTables::of($users)
            ->addColumn('role', function ($user) {
                return $user->roles->pluck('name')->join(', ');
            })

            ->addColumn('action', function ($user) {
                if ($user->hasRole('admin')) {
                    return '<span class="badge bg-secondary">Not Allowed</span>';
                }
                $edit = '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                $delete = '<form method="POST" action="' . route('admin.users.destroy', $user->id) . '" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                           </form>';
                return $edit . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {

        // $request->validate([
        //     'name'=>'required',
        //     'email'=>'required|email|unique:users',
        //     'password'=>'required|min:6',
        //     'role'=>'required',
        //     'number' =>'required',
        //     'state'=>'required',
        //     'city' =>'required',
        //     'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        //     'category_ids' =>'required',
        // ]);
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
            'state' => $request->state,
            'city' => $request->city,
            'image' => $imagePath,
            'category_ids' => $request->category_ids,
        ]);
        // dd($user);
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to edit admin user');
        }
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required'
        ]);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to update admin user');
        }
        $user->update($request->only('name', 'email', 'role'));

        $user->syncRoles($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
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
}
