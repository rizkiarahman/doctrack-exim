<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Display the PDF Editor tool page.
     */
    public function index()
    {
        return view('pdf.index');
    }
}
