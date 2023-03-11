<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PdfController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function salesPdf(Request $request) {
        $pdf = PDF::loadView();
    }
}
