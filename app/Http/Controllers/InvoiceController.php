<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Invoice received successfully',
            'data' => $request->all()
        ]);
    }
}