<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers',
            'password' => 'required|min:6',
            'number'   => 'required',
            'state'    => 'required',
            'city'     => 'required',
            'image'    => 'nullable|image',
            'category_ids'   => 'required|array',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $destination = public_path('uploads/customer');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image = $request->file('image');
            $name  = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move($destination, $name);

            $imagePath = $name;
        }

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'number'   => $request->number,
            'state'    => $request->state,
            'city'     => $request->city,
            'image'    => $imagePath,
            'status'   => 0,
            'category_ids' => json_encode($request->category_ids),
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'data'    => $customer
        ], 201);
    }

        public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($customer->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Account disabled'
            ], 403);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'data'    => $customer
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

}
