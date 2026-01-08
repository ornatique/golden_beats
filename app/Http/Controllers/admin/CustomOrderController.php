<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomOrderController extends Controller
{
    public function index()
    {
        return view('admin.custom_orders.index');
    }

    public function data()
    {
        $orders = CustomOrder::with('user')->latest();

        return DataTables::of($orders)
            ->addIndexColumn()

            ->addColumn('user_name', fn($o) => $o->user->name ?? '-')

            ->addColumn('image', function ($o) {
                return $o->image
                    ? '<img src="'.asset($o->image).'" width="50">'
                    : '-';
            })

            ->addColumn('status', function ($o) {
                return '<span class="badge bg-info">'.$o->status.'</span>';
            })

            ->addColumn('action', function ($o) {
                return '
                    <a href="'.route('admin.custom-orders.edit',$o->id).'"
                       class="btn btn-primary btn-sm">Edit</a>

                    <a href="'.route('admin.custom-orders.print',$o->id).'"
                       class="btn btn-success btn-sm" target="_blank">Print</a>

                    <button class="btn btn-danger btn-sm"
                        onclick="deleteOrder('.$o->id.')">Delete</button>
                ';
            })

            ->rawColumns(['image','status','action'])
            ->make(true);
    }

    public function edit(CustomOrder $customOrder)
    {
        return view('admin.custom_orders.edit', compact('customOrder'));
    }

    public function update(Request $request, CustomOrder $customOrder)
    {
        $data = $request->validate([
            'description' => 'nullable',
            'remarks' => 'nullable',
            'status' => 'required',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/custom_orders','public');
            $data['image'] = $path;
        }

        $customOrder->update($data);

        return redirect()->route('admin.custom-orders.index')
            ->with('success','Custom Order updated');
    }

    public function destroy(CustomOrder $customOrder)
    {
        $customOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }

    public function print(CustomOrder $customOrder)
    {
        return view('admin.custom_orders.print', compact('customOrder'));
    }
}

