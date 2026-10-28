<?php

namespace App\Services;

use App\Models\Appointment;

class AppointmentService
{
    /**
     * Create a new appointment
     */
    public function createAppointment(array $data)
    {
        // Validation: Artist availability
        if (!$this->isArtistAvailable($data['artist_id'], $data['appointment_date'], $data['appointment_time'])) {
            throw new \Exception('The selected artist is not available at this time.');
        }

        // Validation: Daily booking limit
        if ($this->isDailyLimitReached($data['appointment_date'])) {
            throw new \Exception('The maximum booking limit for this day has been reached.');
        }

        $data['status'] = 'pending';
        return Appointment::create($data);
    }

    public function isArtistAvailable($artistId, $date, $time, $excludeId = null)
    {
        $dateCarbon = \Carbon\Carbon::parse($date);
        $dayName = strtolower($dateCarbon->format('l'));
        $timeFormatted = \Carbon\Carbon::parse($time)->format('H:i:s');

        // Shop is closed on Mondays
        if ($dateCarbon->dayOfWeek === 1) {
            return false;
        }

        if ($artistId) {
            $artist = \App\Models\Artist::find($artistId);
            if ($artist) {
                // If today and artist is marked 'off'
                if ($date === now()->toDateString() && $artist->live_status === 'off') {
                    return false;
                }

                // Check weekly working hours
                if (!empty($artist->working_hours) && is_array($artist->working_hours)) {
                    $dayConfig = $artist->working_hours[$dayName] ?? null;
                    if ($dayConfig) {
                        if (empty($dayConfig['is_working'])) {
                            return false; // Day off
                        }
                        $startTime = !empty($dayConfig['start']) ? $dayConfig['start'] . ':00' : '09:00:00';
                        $endTime = !empty($dayConfig['end']) ? $dayConfig['end'] . ':00' : '18:00:00';
                        if ($timeFormatted < $startTime || $timeFormatted > $endTime) {
                            return false; // Outside artist working hours
                        }
                    }
                }

                // Check queue load if booking is for today
                if ($date === now()->toDateString()) {
                    $activeWaitMinutes = $artist->estimated_wait_minutes;
                    if ($activeWaitMinutes > 0) {
                        $busyUntil = now()->addMinutes($activeWaitMinutes);
                        $slotDateTime = \Carbon\Carbon::parse($date . ' ' . $timeFormatted);
                        if ($slotDateTime->lt($busyUntil)) {
                            return false; // Overlaps with current live queue load
                        }
                    }
                }
            }
        }

        // 1. Check for existing appointments
        $query = Appointment::where('appointment_date', $date)
            ->where('appointment_time', $timeFormatted)
            ->whereIn('status', ['pending', 'confirmed', 'in-progress']);
            
        if ($artistId) {
            $query->where('artist_id', $artistId);
        } else {
            $query->whereNull('artist_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            return false;
        }

        // 2. Check for approved "Close" schedule requests
        if ($artistId) {
            $isBlocked = \App\Models\ScheduleRequest::where('artist_id', $artistId)
                ->where('date', $date)
                ->where('status', 'approved')
                ->where('type', 'close')
                ->where(function ($q) use ($timeFormatted) {
                    $q->whereNull('time') // Full day block
                      ->orWhere('time', $timeFormatted); // Specific hour block
                })
                ->exists();

            if ($isBlocked) {
                return false;
            }
        }

        return true;
    }


    public function isDailyLimitReached($date, $limit = 10)
    {
        return Appointment::where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'in-progress'])
            ->count() >= $limit;
    }

    public function getAppointments($searchTerm = null, $serviceId = null, $sortBy = 'appointment_date', $order = 'desc')
    {
        $query = Appointment::with(['service', 'artist', 'user'])
            ->select('appointments.*');

        // Search Logic
        $query->when($searchTerm, function ($q, $search) {
            $q->where('client_name', 'like', "%{$search}%")
              ->orWhereHas('artist', function($sub) use ($search) {
                  $sub->where('name', 'like', "%{$search}%");
              });
        });

        // Filter Logic
        $query->when($serviceId && $serviceId !== 'All', function ($q) use ($serviceId) {
            $q->where('service_id', $serviceId);
        });

        // Sorting Logic
        if ($sortBy === 'artist') {
            $query->join('artists', 'appointments.artist_id', '=', 'artists.id')
                  ->orderBy('artists.name', $order);
        } elseif ($sortBy === 'service') {
            $query->join('services', 'appointments.service_id', '=', 'services.id')
                  ->orderBy('services.name', $order);
        } elseif ($sortBy === 'client') {
            $query->orderBy('client_name', $order);
        } elseif ($sortBy === 'status') {
            $query->orderBy('status', $order);
        } else {
            // Default: appointment_date
            $query->orderBy('appointment_date', $order)
                  ->orderBy('appointment_time', $order);
        }

        return $query->get();
    }
}
