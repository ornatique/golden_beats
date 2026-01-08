<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use File;
use Carbon\Carbon;


class OrderController extends Controller
{
    // 👉 View only
    public function index()
    {
        return view('admin.orders.index');
    }

    // 👉 DataTable AJAX
    public function getData()
    {
        $orders = Order::query()
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select([
                'orders.id',
                'orders.order_id',
                'users.name as user_name',
                'orders.product_id',
                'orders.quantity',
                'orders.remarks',
                'orders.weight',
                'orders.status',
                'orders.created_at',
            ])
            ->latest('orders.id');

        return DataTables::of($orders)
            ->addIndexColumn()
            ->filterColumn('user_name', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->editColumn('status', function ($row) {

                $statuses = ['Pending', 'Approval', 'Making', 'Finishing', 'Done'];

                $select = '<select class="form-select form-select-sm order-status form-control"
                    data-id="' . $row->id . '" style="width:150px">';

                foreach ($statuses as $status) {
                    $selected = $row->status === $status ? 'selected' : '';
                    $select .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                }

                $select .= '</select>';

                return $select;
            })

            ->editColumn('remarks', function ($row) {
                return '<span class="">' . ucfirst($row->remarks) . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y h:i A');
            })

            // ✅ PRINT + PDF ONLY
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.orders.print', $row->order_id) . '"
                    class="btn btn-success btn-sm" target="_blank">
                        Print
                    </a>

                    <a href="' . route('admin.orders.pdf', $row->order_id) . '"
                    class="btn btn-danger btn-sm ml-1" target="_blank">
                        PDF
                    </a>
                    <button class="btn btn-danger btn-sm"
                        onclick="deleteOrder(' . $row->id . ')">
                        Delete
                    </button>
                ';
            })

            ->rawColumns(['status', 'action', 'remarks'])
            ->make(true);
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    private function invoiceData($order_id)
    {
        $order = Order::with(['user', 'product.category'])
            ->where('order_id', $order_id)
            ->firstOrFail();

        return [
            'order_id'       => $order,
            'data'           => collect([$order]), // 👈 keep blade loop working
            'total_quantity' => $order->quantity,
        ];
    }


    public function print(Request $request, $order_id)
    {

        $data = $this->invoiceData($order_id);
        $data['is_pdf'] = false;
        return view('admin.orders.print', $data);
    }


    public function pdf($order_id)
    {
        $data = $this->invoiceData($order_id);
        $data['is_pdf'] = true;

        $pdf = Pdf::loadView('admin.orders.print', $data)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('invoice-' . $order_id . '.pdf');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:orders,id',
            'status' => 'required|string',
        ]);

        Order::where('id', $request->id)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order'
            ], 500);
        }
    }
}
