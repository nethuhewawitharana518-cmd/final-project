<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentLedgerController extends Controller
{
    /**
     * Admin payment ledger — all Stripe transactions.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'reservation.business'])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by transaction ID or reservation code
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', "%{$s}%")
                  ->orWhereHas('reservation', fn($r) => $r->where('reservation_code', 'like', "%{$s}%"));
            });
        }

        $payments = $query->paginate(25)->withQueryString();

        // Summary stats
        $stats = [
            'total_revenue'   => Payment::where('status', 'success')->sum('amount'),
            'total_count'     => Payment::where('status', 'success')->count(),
            'pending_count'   => Payment::where('status', 'pending')->count(),
            'failed_count'    => Payment::where('status', 'failed')->count(),
            'refunded_amount' => Payment::where('status', 'refunded')->sum('amount'),
        ];

        return view('admin.payment-ledger', compact('payments', 'stats'));
    }

    /**
     * Export payments as CSV.
     */
    public function export(Request $request)
    {
        $payments = Payment::with(['user', 'reservation.business'])
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->get();

        $csv = "Transaction ID,Reservation Code,Customer,Business,Amount (LKR),Card,Status,Date\n";
        foreach ($payments as $p) {
            $csv .= implode(',', [
                $p->transaction_id ?? 'N/A',
                $p->reservation?->reservation_code ?? 'N/A',
                $p->user?->name ?? 'N/A',
                $p->reservation?->business?->business_name ?? 'N/A',
                $p->amount,
                $p->card_display,
                $p->status,
                $p->paid_at?->format('Y-m-d H:i:s') ?? 'N/A',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
