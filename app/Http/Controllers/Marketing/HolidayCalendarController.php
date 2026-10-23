<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayCalendarController extends Controller
{
    /**
     * Marketing panel: View-only holiday calendar
     */
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month'); // optional

        $query = Holiday::query();

        if ($month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        } else {
            $query->whereYear('date', $year);
        }

        $holidays = $query->orderBy('date')->get();

        // Calculate stats
        $nationalCount = $holidays->where('type', 'national')->count();
        $companyCount = $holidays->where('type', 'company')->count();
        $totalHolidays = $holidays->count();
        
        // Count upcoming holidays (from today)
        $upcomingCount = $holidays->filter(function($holiday) {
            return $holiday->date >= now()->startOfDay();
        })->count();

        // Prepare calendar events (FullCalendar format)
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
                    'description' => $holiday->description,
                ],
            ];
        })->toArray();

        return view('Marketing.holidays.calendar', compact(
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