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
            ->addColumn('role', function($user){
                return $user->roles->pluck('name')->join(', ');
            })
            
            ->addColumn('action', function($user){
                $edit = '<a href="'.route('admin.users.edit', $user->id).'" class="btn btn-sm btn-primary">Edit</a>';
                $delete = '<form method="POST" action="'.route('admin.users.destroy', $user->id).'" style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                           </form>';
                return $edit.' '.$delete;
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
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6',
            'role'=>'required'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success','User created successfully');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to edit admin user');
        }
        $roles = Role::all();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'role'=>'required'
        ]);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You are not allowed to update admin user');
        }
        $user->update($request->only('name','email'));

        $user->syncRoles($request->role);

        return redirect()->route('admin.users.index')->with('success','User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
        return back()->with('error', 'Admin user cannot be deleted');
         }
        $user->delete();
        return back()->with('success','User deleted successfully');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
