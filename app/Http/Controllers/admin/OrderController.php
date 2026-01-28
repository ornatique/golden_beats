<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use File;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select([
                DB::raw('MIN(orders.id) as id'),          // single row id
                'orders.order_id',
                'customers.name as user_name',
                DB::raw('SUM(orders.quantity) as quantity'),
                DB::raw('MAX(orders.weight) as weight'),
                DB::raw('MAX(orders.remarks) as remarks'),
                DB::raw('MAX(orders.status) as status'),
                DB::raw('MAX(orders.created_at) as created_at'),
            ])
            ->groupBy('orders.order_id', 'customers.name')
            ->latest('id');

        return DataTables::of($orders)
            ->addIndexColumn()

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->where('customers.name', 'like', "%{$keyword}%");
            })

            ->editColumn('status', function ($row) {
                $statuses = ['Pending', 'Approval', 'Making', 'Finishing', 'Done'];

                $select = '<select class="form-select form-select-sm order-status form-control"
                data-id="' . $row->order_id . '" style="width:150px">';

                foreach ($statuses as $status) {
                    $selected = $row->status === $status ? 'selected' : '';
                    $select .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                }

                $select .= '</select>';

                return $select;
            })

            ->editColumn('remarks', function ($row) {
                return $row->remarks ? ucfirst($row->remarks) : '-';
            })

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y h:i A');
            })

            ->addColumn('action', function ($row) {

                $html = '';

                if (auth()->user()->can('order-print')) {
                    $html .= '
                    <a href="' . route('admin.orders.print', $row->order_id) . '"
                       class="btn btn-success btn-sm mr-1"
                       target="_blank">
                        Print
                    </a>
                ';
                }

                if (auth()->user()->can('order-pdf')) {
                    $html .= '
                    <a href="' . route('admin.orders.pdf', $row->order_id) . '"
                       class="btn btn-danger btn-sm mr-1"
                       target="_blank">
                        PDF
                    </a>
                ';
                }

                if (auth()->user()->can('order-delete')) {
                    $html .= '
                    <button class="btn btn-danger btn-sm"
                        onclick="deleteOrderByOrderId(\'' . $row->order_id . '\')">
                        Delete
                    </button>
                ';
                }

                return $html ?: '-';
            })

            ->rawColumns(['status', 'action'])
            ->make(true);
    }


    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    private function invoiceData($order_id)
    {
        // 🔹 Fetch all products under same order_id
        $orders = Order::with(['customer', 'product.category'])
            ->where('order_id', $order_id)
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        // 🔹 Use first row for common order info
        $orderInfo = $orders->first();

        return [
            'order_id'       => $order_id,
            'order'          => $orderInfo,
            'data'           => $orders, // 👈 MULTIPLE PRODUCTS
            'total_quantity' => $orders->sum('quantity'),
            'total_weight'   => $orders->sum('weight'),
            'status'         => $orderInfo->status,
            'remarks'        => $orderInfo->remarks,
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
            // 'order_id'     => 'required|exists:orders,order_id',
            'status' => 'required|string',
        ]);

        Order::where('order_id', $request->id)
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
