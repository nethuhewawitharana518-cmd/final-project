<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    /**
     * Display earnings summary and payouts.
     */
    public function index()
    {
        $business = Auth::user()->business;
        
        $commissions = $business->commissions()
            ->latest()
            ->paginate(15);

        $totalSales = (float) $business->commissions()->sum('sale_amount');
        $totalCommission = (float) $business->commissions()->sum('commission_amount');
        $totalEarnings = (float) $business->commissions()->sum('business_earnings');

        return view('business.earnings', compact(
            'commissions',
            'totalSales',
            'totalCommission',
            'totalEarnings'
        ));
    }

    /**
     * Display detailed commissions history list.
     */
    public function commissions()
    {
        $business = Auth::user()->business;
        
        $commissions = $business->commissions()
            ->latest()
            ->paginate(15);

        return view('business.commissions', compact('commissions'));
    }

    /**
     * Export earnings report as CSV file download.
     */
    public function export()
    {
        $business = Auth::user()->business;
        $commissions = $business->commissions()->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="earnings-report.csv"',
        ];

        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Date', 'Gross Sale (Rs.)', 'Commission (Rs.)', 'Net Earnings (Rs.)', 'Status']);

            foreach ($commissions as $row) {
                fputcsv($file, [
                    $row->reservation_id,
                    $row->created_at->format('Y-m-d H:i'),
                    number_format($row->sale_amount, 2),
                    number_format($row->commission_amount, 2),
                    number_format($row->business_earnings, 2),
                    $row->status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
