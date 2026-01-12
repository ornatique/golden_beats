<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

            // ✅ Capitalize first letter of username
            ->addColumn('user_name', function ($o) {
                return $o->user ? ucfirst($o->user->name) : '-';
            })

            // 🔥 ENABLE SEARCH ON USER NAME
            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('image', function ($o) {
                return $o->image
                    ? '<img src="' . asset($o->image) . '" width="50">'
                    : '-';
            })

            // ✅ STATUS DROPDOWN
            ->addColumn('status', function ($o) {

                $statuses = ['Pending', 'Approval', 'Making', 'Finishing', 'Done'];

                $html = '<select class="form-control form-control-sm order-status"
                        data-id="' . $o->id . '">';

                foreach ($statuses as $status) {
                    $selected = $o->status === $status ? 'selected' : '';
                    $html .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                }

                $html .= '</select>';

                return $html;
            })
            ->editColumn('created_at', function ($o) {
                return Carbon::parse($o->created_at)->format('d M Y h:i A');
            })
            ->addColumn('action', function ($o) {

                $html = '';

                // ✏️ EDIT
                if (auth()->user()->can('custom-order-edit')) {
                    $html .= '
            <a href="' . route('admin.custom-orders.edit', $o->id) . '"
               class="btn btn-primary btn-sm mr-1">
                Edit
            </a>
        ';
                }

                // 🖨 PRINT
                if (auth()->user()->can('custom-order-print')) {
                    $html .= '
            <a href="' . route('admin.custom-orders.print', $o->id) . '"
               class="btn btn-success btn-sm mr-1"
               target="_blank">
                Print
            </a>
        ';
                }

                // 🗑 DELETE
                if (auth()->user()->can('custom-order-delete')) {
                    $html .= '
            <button class="btn btn-danger btn-sm"
                onclick="deleteOrder(' . $o->id . ')">
                Delete
            </button>
        ';
                }

                return $html ?: '-';
            })
            ->rawColumns(['action'])


            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }


    public function edit(CustomOrder $customOrder)
    {
        return view('admin.custom_orders.edit', compact('customOrder'));
    }

    public function update(Request $request, CustomOrder $customOrder)
    {
        $data = $request->validate([
            'remarks' => 'nullable',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destination = public_path('uploads/custom_orders');

            //  create folder if not exists
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            //  move file to public folder
            $file->move($destination, $filename);

            //  save relative path in DB
            $data['image'] = 'uploads/custom_orders/' . $filename;
        }

        $customOrder->update($data);

        return redirect()
            ->route('admin.custom-orders.index')
            ->with('success', 'Custom Order updated successfully');
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

    public function updateStatus(Request $request, CustomOrder $customOrder)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $customOrder->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated'
        ]);
    }
}
