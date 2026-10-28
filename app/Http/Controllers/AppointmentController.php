<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $appointmentService;

    public function __construct(\App\Services\AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $serviceId = $request->input('service_id');
        
        $appointments = $this->appointmentService->getAppointments($searchTerm, $serviceId);
        $services = \App\Models\Service::all();

        return view('pages.appointments.index', compact('appointments', 'services', 'searchTerm', 'serviceId'));
    }

    public function create()
    {
        // View handled by modal in index page usually, but could be a separate page
        return view('pages.appointments.create');
    }

    public function store(\App\Http\Requests\StoreAppointmentRequest $request)
    {
        $this->appointmentService->createAppointment($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Appointment successfully created.');
    }

    public function show(\App\Models\Appointment $appointment)
    {
        return view('pages.appointments.show', compact('appointment'));
    }

    public function edit(\App\Models\Appointment $appointment)
    {
        $services = \App\Models\Service::all();
        return view('pages.appointments.edit', compact('appointment', 'services'));
    }

    public function update(Request $request, \App\Models\Appointment $appointment)
    {
        $appointment->update($request->all());
        return redirect()->route('appointments.index')->with('success', 'Appointment successfully updated.');
    }

    public function destroy(\App\Models\Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment successfully deleted.');
    }
}
