@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Appointments</h1>
            <p class="text-muted-foreground">Manage your bookings and schedule</p>
        </div>
        <button onclick="document.getElementById('new-appointment-modal').classList.remove('hidden')" class="flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
            <span>+ New Appointment</span>
        </button>
    </div>

    <!-- Filters -->
    <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Search appointments..." class="w-full px-4 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" />
        </div>
        <div class="flex items-center gap-2">
            <select name="service_id" onchange="this.form.submit()" class="px-4 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="All">All Services</option>
                @foreach($services as $serviceOption)
                    <option value="{{ $serviceOption->id }}" {{ ($serviceId ?? '') == $serviceOption->id ? 'selected' : '' }}>
                        {{ $serviceOption->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Filter</button>
        </div>
    </form>

    <!-- Calendar View / List -->
    <div class="bg-card border border-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Schedule</h3>
        </div>

        <div class="space-y-3">
            @forelse($appointments as $apt)
                <div class="flex items-center justify-between p-4 bg-muted rounded-lg hover:bg-muted/80 transition-colors">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="text-center min-w-[80px]">
                            <div class="font-bold text-primary">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</div>
                            <div class="text-xs text-muted-foreground">{{ $apt->duration ?? '30 min' }}</div>
                        </div>
                        <div class="h-12 w-px bg-border"></div>
                        <div class="flex-1">
                            <div class="font-semibold">{{ $apt->client_name }}</div>
                            <div class="text-sm text-muted-foreground">{{ $apt->service->name ?? 'Service' }} • {{ $apt->artist_name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $apt->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($apt->status === 'in-progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-800') }}">
                            {{ ucfirst($apt->status) }}
                        </span>
                        <form action="{{ route('appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-muted-foreground">
                    No appointments found matching your criteria.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- New Appointment Modal (Simplified Demo) -->
<div id="new-appointment-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Book New Appointment</h3>
        <form action="{{ route('appointments.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-2">Client Name <span class="text-red-500">*</span></label>
                    <input type="text" name="client_name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone_number" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm mb-2">Service <span class="text-red-500">*</span></label>
                    <select name="service_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        @foreach($services as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2">Artist <span class="text-red-500">*</span></label>
                    <select name="artist_name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="Pedro">Pedro</option>
                        <option value="Maya">Maya</option>
                        <option value="Rico">Rico</option>
                        <option value="Lisa">Lisa</option>
                        <option value="Jay">Jay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="appointment_date" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm mb-2">Time <span class="text-red-500">*</span></label>
                    <input type="time" name="appointment_time" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('new-appointment-modal').classList.add('hidden')" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
