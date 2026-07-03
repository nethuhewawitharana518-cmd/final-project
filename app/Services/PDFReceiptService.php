<?php

namespace App\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFReceiptService
{
    /**
     * Generate a PDF invoice/receipt for a reservation.
     */
    public function generate(Reservation $reservation)
    {
        return Pdf::loadView('pdf.receipt', compact('reservation'));
    }
}
