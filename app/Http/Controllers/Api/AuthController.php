<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\PersonalAccessToken;

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
            'device_token'=>$request->device_token
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
    Log::info('LOGIN REQUEST', $request->all());

    $validator = Validator::make($request->all(), [
        'number'       => 'required',
        'password'     => 'required',
        'device_token' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => '0',
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // ✅ FETCH CUSTOMER (NOT USER)
    $customer = Customer::where('number', $request->number)->first();

    if (!$customer) {
        return response()->json([
            'status'  => '0',
            'message' => 'Customer not found. Please contact support.',
        ], 200);
    }

    if ($customer->status != 1) {
        return response()->json([
            'status'  => '0',
            'message' => 'Account inactive. Please contact support.',
        ], 200);
    }

    if (!Hash::check($request->password, $customer->password)) {
        return response()->json([
            'status'  => '0',
            'message' => 'Invalid login details',
        ], 401);
    }

    /**
     * ✅ SINGLE DEVICE LOGIN LOGIC
     */
    if (!$customer->device_token) {
        $customer->device_token = $request->device_token;
    } elseif ($customer->device_token !== $request->device_token) {
        return response()->json([
            'status'  => '0',
            'message' => 'Already logged in from another device.',
        ], 200);
    }

    /**
     * ✅ SAVE OPTIONAL DATA
     */
    $customer->fcm_token   = $request->fcm_token ?? null;
    $customer->device_type = $request->device_type ?? null;
    $customer->save();

    /**
     * 🔥 IMPORTANT: DELETE OLD TOKENS
     */
    $customer->tokens()->delete();

    /**
     * ✅ CREATE NEW TOKEN
     */
    $token = $customer->createToken('customer-token')->plainTextToken;

    return response()->json([
        'status'        => '1',
        'message'       => 'Login successful',
        'token'         => $token,
        'image_url'     => asset('public/assets/images/users'),
        'device_token'  => $customer->device_token,
        'data'          => $customer,
    ], 200);
}

    public function profile(Request $request)
    {
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'success' => false,
            'message' => 'Token missing'
        ], 401);
    }

    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken || !$accessToken->tokenable instanceof \App\Models\Customer) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid token'
        ], 401);
    }

    // ✅ IGNORE request->id COMPLETELY
    $customer = $accessToken->tokenable;

    return response()->json([
        'success' => true,
        'data' => $customer
    ]);
}
}
