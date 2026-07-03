<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;

class QRScannerController extends Controller
{
    /**
     * Display in-app QR scanner interface.
     */
    public function index()
    {
        return view('business.scanner');
    }
}
