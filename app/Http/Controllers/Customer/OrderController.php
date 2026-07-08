<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\PDFReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private PDFReceiptService $pdfService) {}

    /**
     * Display list of customer orders.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Auth::user()->reservations()->with(['business', 'items', 'review']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10);

        return view('customer.orders', compact('orders', 'status'));
    }

    /**
     * Show single order details.
     */
    public function show($id)
    {
        $order = Auth::user()->reservations()->with(['business', 'items.food', 'qrCode', 'payment'])->findOrFail($id);
        return view('customer.order-detail', compact('order'));
    }

    /**
     * Display QR code verification page for an order.
     */
    public function qrPage($id)
    {
        $order = Auth::user()->reservations()->with(['business', 'qrCode'])->findOrFail($id);
        return view('customer.order-qr', compact('order'));
    }

    /**
     * Download PDF invoice for an order.
     */
    public function downloadReceipt($id)
    {
        $order = Auth::user()->reservations()->with(['business', 'items.food', 'payment'])->findOrFail($id);
        $pdf = $this->pdfService->generate($order);
        
        return $pdf->download("receipt-{$order->reservation_code}.pdf");
    }

    /**
     * Cancel a pending reservation.
     */
    public function cancel($id)
    {
        $order = Auth::user()->reservations()->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be cancelled.');
        }

        // Return quantities back to stock
        foreach ($order->items as $item) {
            if ($item->food) {
                $item->food->increment('available_quantity', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('customer.orders')->with('success', 'Reservation cancelled successfully.');
    }
}
