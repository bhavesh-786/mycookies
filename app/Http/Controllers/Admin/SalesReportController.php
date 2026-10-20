<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capture filters (Defaults to the current year/month to ensure past and future test orders appear)
        $startDate = $request->input('start_date', '2026-01-01');
        $endDate   = $request->input('end_date', '2026-12-31');
        $groupBy   = $request->input('group_by', 'none');
        $branchId  = $request->input('branch_id', 'all');

        // 2. Base query on orders and left join stores
        $query = DB::table('orders')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id');

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(orders.created_at)'), [$startDate, $endDate]);
        }

        // Apply branch filter if selected
        if ($branchId && $branchId !== 'all') {
            if ($branchId === 'online') {
                $query->whereNull('orders.store_id');
            } else {
                $query->where('orders.store_id', $branchId);
            }
        }

        // 3. Metric Aggregations matching your exact table columns
        $selects = [
            DB::raw('COALESCE(stores.name, "Online / Delivery") as branch_name'),
            DB::raw('COALESCE(orders.store_id, 0) as branch_id'),
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('CAST(COALESCE(SUM(orders.subtotal), 0) AS DECIMAL(10,3)) as gross_sales'),
            DB::raw('CAST(COALESCE(SUM(orders.delivery_fee), 0) AS DECIMAL(10,3)) as total_delivery_fees'),
            DB::raw('CAST(COALESCE(SUM(orders.total), 0) AS DECIMAL(10,3)) as net_sales'),
            DB::raw('0.000 as total_discounts')
        ];

        // 4. Dynamic Group By definitions
        $groupByClauses = [
            DB::raw('COALESCE(stores.name, "Online / Delivery")'),
            DB::raw('COALESCE(orders.store_id, 0)')
        ];

        if ($groupBy === 'day') {
            $selects[] = DB::raw('DATE(orders.created_at) as group_period');
            $groupByClauses[] = DB::raw('DATE(orders.created_at)');
        } elseif ($groupBy === 'week') {
            $selects[] = DB::raw('CONCAT("Week ", YEARWEEK(orders.created_at)) as group_period');
            $groupByClauses[] = DB::raw('YEARWEEK(orders.created_at)');
        } elseif ($groupBy === 'month') {
            $selects[] = DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as group_period');
            $groupByClauses[] = DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m")');
        } elseif ($groupBy === 'order_type') {
            $selects[] = DB::raw('COALESCE(orders.order_type, "Standard") as group_period');
            $groupByClauses[] = DB::raw('COALESCE(orders.order_type, "Standard")');
        }

        $reportData = $query->select($selects)
            ->groupBy($groupByClauses)
            ->orderByDesc('gross_sales')
            ->get();

        // 5. Calculate Footnote Totals
        $totals = [
            'total_orders'        => $reportData->sum('total_orders'),
            'gross_sales'         => $reportData->sum('gross_sales'),
            'total_discounts'     => 0.000,
            'total_delivery_fees' => $reportData->sum('total_delivery_fees'),
            'net_sales'           => $reportData->sum('net_sales'),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data'   => $reportData,
                'totals' => $totals,
            ]);
        }

        // Fetch stores using your exact table columns (id, name, location_description, is_active)
        $branches = DB::table('stores')
            ->select('id', 'name', 'location_description', 'is_active')
            ->orderBy('name')
            ->get();

        return view('admin.reports.sales_by_branch', compact('branches', 'startDate', 'endDate'));
    }

    public function export(Request $request, $type = 'csv')
    {
        $startDate = $request->input('start_date', '2026-01-01');
        $endDate   = $request->input('end_date', '2026-12-31');
        $branchId  = $request->input('branch_id', 'all');

        $query = DB::table('orders')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id');

        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(orders.created_at)'), [$startDate, $endDate]);
        }

        if ($branchId && $branchId !== 'all') {
            $query->where('orders.store_id', $branchId);
        }

        $records = $query->select([
            DB::raw('COALESCE(stores.name, "Online / Delivery") as branch_name'),
            DB::raw('COALESCE(orders.store_id, 0) as branch_ref'),
            DB::raw('COUNT(orders.id) as orders_count'),
            DB::raw('SUM(orders.subtotal) as gross_sales'),
            DB::raw('SUM(orders.delivery_fee) as delivery_fee'),
            DB::raw('SUM(orders.total) as net_sales')
        ])
            ->groupBy(DB::raw('COALESCE(stores.name, "Online / Delivery")'), DB::raw('COALESCE(orders.store_id, 0)'))
            ->get();

        $filename = "sales_by_branch_{$startDate}_{$endDate}.csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Branch', 'Branch ID', 'Orders Count', 'Gross Sales (KD)', 'Delivery Fees (KD)', 'Net Sales (KD)']);
            foreach ($records as $row) {
                fputcsv($file, [
                    $row->branch_name,
                    $row->branch_ref,
                    $row->orders_count,
                    number_format($row->gross_sales, 3),
                    number_format($row->delivery_fee, 3),
                    number_format($row->net_sales, 3),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
