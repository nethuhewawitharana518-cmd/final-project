<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Commission;
use App\Models\Reservation;
use App\Models\Food;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports administration index.
     */
    public function index()
    {
        $monthlyIncome = (float) Payment::where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalFoodUploads = Food::count();

        $completedOrdersCount = Reservation::where('status', 'collected')->count();

        $topBusinesses = Business::withCount(['reservations' => function($query) {
                $query->where('status', 'collected');
            }])
            ->orderBy('reservations_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports', compact(
            'monthlyIncome',
            'totalFoodUploads',
            'completedOrdersCount',
            'topBusinesses'
        ));
    }

    /**
     * Export system metrics as CSV downloads.
     */
    public function export(Request $request, $type)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"report-{$type}.csv\"",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'food_waste') {
                fputcsv($file, ['Business', 'Food Item', 'Category', 'Original Quantity', 'Available Quantity', 'Food Waste Saved (Kg)']);
                $foods = Food::with(['business', 'category'])->get();
                foreach ($foods as $food) {
                    $saved = ($food->quantity - $food->available_quantity) * 0.5;
                    fputcsv($file, [
                        $food->business->business_name,
                        $food->name,
                        $food->category->name,
                        $food->quantity,
                        $food->available_quantity,
                        $saved
                    ]);
                }
            } elseif ($type === 'revenue') {
                fputcsv($file, ['Payment ID', 'User ID', 'Amount (Rs.)', 'Gateway', 'Transaction ID', 'Status', 'Date']);
                $payments = Payment::all();
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->id,
                        $payment->user_id,
                        number_format($payment->amount, 2),
                        $payment->gateway,
                        $payment->transaction_id,
                        $payment->status,
                        $payment->created_at->format('Y-m-d H:i')
                    ]);
                }
            } else {
                fputcsv($file, ['Placeholder', 'No data matched this type']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
