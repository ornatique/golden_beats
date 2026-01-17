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
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.customers.index');
    }


    private function userListQuery(Request $request)
    {
     
       $query = Customer::with('roles')
    ->where('status', request('status') === 'inactive' ? 0 : 1)
    ->orderBy('id', 'desc');

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

        // 🔹 Status filter
        if ($request->filled('status')) {
            $users->where('status', $request->status === 'active' ? 1 : 0);
        }

        return DataTables::of($users)

            ->addIndexColumn()

            ->addColumn('profile_image', function ($user) {
                return $user->image
                    ? '<img src="'.asset($user->image).'"
                            width="80" height="80"
                            style="object-fit:cover;border-radius:60%;">'
                    : '<span class="badge bg-secondary">No Image</span>';
            })

            ->addColumn('contact', fn($user) => $user->number ?? '-')

            ->addColumn('reg_date', function ($user) {
                return $user->created_at
                    ? $user->created_at->format('d-m-Y H:i')
                    : '-';
            })

            ->addColumn('role', fn($user) => ucfirst($user->role))

            ->addColumn('status', function ($user) {

                $checked  = $user->status ? 'checked' : '';
                $disabled = $user->role === 'admin' ? 'disabled' : '';

                return '
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                            class="custom-control-input toggle-status"
                            id="statusSwitch'.$user->id.'"
                            data-id="'.$user->id.'"
                            '.$checked.' '.$disabled.'>
                        <label class="custom-control-label"
                            for="statusSwitch'.$user->id.'"></label>
                    </div>
                ';
            })

            ->addColumn('action', function ($user) {

                $buttons = '';

                if (auth()->user()->can('customers-edit')) {
                    $buttons .= '
                        <a href="'.route('admin.customers.edit',$user->id).'"
                        class="btn btn-sm btn-primary mr-1">
                        Edit
                        </a>
                    ';
                }

                if (auth()->user()->can('customers-delete')) {
                    $buttons .= '
                        <form method="POST"
                            action="'.route('admin.customers.destroy',$user->id).'"
                            style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm(\'Are you sure?\')">
                                Delete
                            </button>
                        </form>
                    ';
                }

                return $buttons ?: '-';
            })

            ->rawColumns(['profile_image', 'status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        $categories = Category::pluck('name', 'id');
        return view('admin.customers.create', compact('roles', 'categories'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
            'number' => 'required',
            'state' => 'required',
            'city' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp',

        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {

            $image = $request->file('image');

            // 🔹 Folder path
            $destinationPath = public_path('uploads/customer');

            // 🔹 Create folder if NOT exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // 🔹 Unique image name
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // 🔹 Upload image
            $image->move($destinationPath, $imageName);

            // 🔹 Save path for DB
            $imagePath = 'uploads/customer/' . $imageName;
        }
        $Customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'number' => $request->number,
            'state' => $request->states,
            'city' => $request->city,
            'image' => $imagePath,
            'category_ids' => json_encode($request->category_ids),
        ]);


        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
    }

    public function edit(customer $customer)
    {
        $selectedCategories = json_decode($customer->category_ids, true) ?? [];
        $roles = Role::all();
        $categories = Category::pluck('name', 'id');
        return view('admin.customers.edit', compact('customer', 'roles', 'categories', 'selectedCategories'));
    }

    public function update(Request $request, Customer $customer)
    {


        // ✅ Validation (EDIT MODE)
        $request->validate([
            'name'   => 'required',
            'email'  => 'required|email',
            'number' => 'required',
            'state'  => 'required',
            'city'   => 'required',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp,gif',
        ]);

        // 🔹 Update basic fields
        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
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

            $destinationPath = public_path('uploads/customer');

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);

            $data['image'] = 'uploads/customer/' . $imageName;
        }

        // 🏷 Category (ONLY if customer)
       
            $data['category_ids'] = json_encode($request->category_ids);
           

        // 🔄 Update user
        $customer->update($data);

        // 🔄 Sync role
        $customer->syncRoles([$request->role]);

        return redirect()
            ->route('admin.customers.index', [
            ])
            ->with('success', 'Customers updated successfully');
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'customers deleted successfully');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function checkEmail(Request $request)
    {
        $exists = Customer::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'exists' => true
            ]);
        }
    }

    public function updateStatus(Request $request)
    {
   
        $request->validate([
            'id' => 'required|exists:customers,id',
            'status' => 'required|boolean',
        ]);

        $Customer = Customer::findOrFail($request->id);
        $Customer->status = $request->status;
        $Customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
