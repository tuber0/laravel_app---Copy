<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('appointment.service', 'appointment.artist')->latest()->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with('appointment.service', 'appointment.artist')->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function print()
    {
        $payments = Payment::with('appointment.service', 'appointment.artist')->latest()->get();
        return view('admin.payments.print', compact('payments'));
    }
}
