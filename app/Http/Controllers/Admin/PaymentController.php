<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('appointment.user', 'appointment.service', 'appointment.artist')->latest()->paginate(15);
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $completedCount = Payment::where('status', 'completed')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        return view('admin.payments.index', compact('payments', 'totalRevenue', 'completedCount', 'pendingAmount'));
    }

    public function show($id)
    {
        $payment = Payment::with('appointment.user', 'appointment.service', 'appointment.artist')->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function print()
    {
        $payments = Payment::with('appointment.user', 'appointment.service', 'appointment.artist')->latest()->get();
        return view('admin.payments.print', compact('payments'));
    }
}
