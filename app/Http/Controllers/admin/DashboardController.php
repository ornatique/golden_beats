<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\Reel;
use App\Models\Event;
use App\Models\CustomOrder;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
       
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
             'totalCustomer'     => Customer::count(),
            'activeCustomers'=> Customer::where('role','customer')->where('status',1)->count(),
            'deactiveCustomers'=> Customer::where('role','customer')->where('status',0)->count(),
            'totalOrders'    => Order::count(),
            'totalReels'     => Reel::count(),
            'totalEvents'    => Event::count(),
            'CustomOrder'    => CustomOrder::count(),
            'Category'    => Category::count(),
            'Subcategory'    => Subcategory::count(),
            'Product'    => Product::count(),

        ]);
    }
}
