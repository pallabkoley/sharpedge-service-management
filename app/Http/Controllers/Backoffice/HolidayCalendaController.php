<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayCalendaController extends Controller
{
    /**
     * Backoffice panel: Holiday calendar (view)
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month); // Add default month

        $query = Holiday::query();

        // Filter by year and month
        if ($month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        } else {
            $query->whereYear('date', $year);
        }

        $holidays = $query->orderBy('date')->get();

        // Stats
        $nationalCount = $holidays->where('type', 'national')->count();
        $companyCount = $holidays->where('type', 'company')->count();
        $totalHolidays = $holidays->count();

        $upcomingCount = $holidays->filter(function ($holiday) {
            return $holiday->date >= now()->startOfDay();
        })->count();

        // For FullCalendar events (optional)
        $calendarEvents = $holidays->map(function ($holiday) {
            return [
                'id' => $holiday->id,
                'title' => $holiday->title,
                'start' => $holiday->date->toDateString(),
                'allDay' => true,
                'color' => $holiday->color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => $holiday->type,
                    'is_default' => $holiday->is_default,
                ],
            ];
        })->toArray();

        return view('Backoffice.holidays.calendar', compact(
            'year',
            'month',
            'holidays',
            'calendarEvents',
            'nationalCount',
            'companyCount',
            'totalHolidays',
            'upcomingCount'
        ));
    }
}