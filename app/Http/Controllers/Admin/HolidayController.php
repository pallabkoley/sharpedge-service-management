<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    /**
     * Show calendar page + holiday list
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
    
    // Prepare calendar events
   $calendarEvents = $holidays->map(function ($holiday) {
    return [
        'id' => $holiday->id,
        'title' => $holiday->title,
        'start' => Carbon::parse($holiday->date)->toDateString(),
        'allDay' => true,
        'color' => $holiday->color,
        'textColor' => '#ffffff',
        'extendedProps' => [
            'type' => $holiday->type,
            'is_default' => $holiday->is_default,
        ],
    ];
})->toArray();


    return view('admin.holidays.index', compact('holidays', 'year', 'month', 'calendarEvents'));
}
    /**
     * Store custom holiday
     */
    public function store(Request $request)
    {
        $request->validate([
            'date'  => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        // prevent duplicate date (since DB has unique(date))
        $exists = Holiday::whereDate('date', $request->date)->exists();
        if ($exists) {
            return back()->with('error', 'Holiday already exists for this date.');
        }

        Holiday::create([
            'date'       => $request->date,
            'title'      => $request->title,
            'type'       => 'custom',
            'is_default' => false,
            'color'      => $request->color ?? '#2563eb',
        ]);

        return back()->with('success', 'Holiday added successfully.');
    }

    /**
     * Delete holiday
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return back()->with('success', 'Holiday deleted successfully.');
    }

    /**
     * Add default holidays (example: all Sundays + 2nd & 4th Saturdays for a year)
     */
    public function addDefaultHolidays(Request $request)
{
    $request->validate([
        'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        'days' => ['required', 'array', 'min:1'],
        'days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
        'occasion' => ['nullable', 'string', 'max:255'],
    ]);

    $year = (int) $request->year;
    $days = $request->days; // selected days array
    $occasion = $request->occasion;

    // Map day name => Carbon dayOfWeek number
    $dayMap = [
        'sunday' => Carbon::SUNDAY,
        'monday' => Carbon::MONDAY,
        'tuesday' => Carbon::TUESDAY,
        'wednesday' => Carbon::WEDNESDAY,
        'thursday' => Carbon::THURSDAY,
        'friday' => Carbon::FRIDAY,
        'saturday' => Carbon::SATURDAY,
    ];

    DB::transaction(function () use ($year, $days, $dayMap, $occasion) {

        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        $datesToInsert = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            foreach ($days as $day) {
                if ($date->dayOfWeek === $dayMap[$day]) {

                    $title = $occasion
                        ? $occasion
                        : ucfirst($day);

                    // color logic (optional)
                    $color = '#2563eb';
                    if ($day === 'sunday') $color = '#ef4444';
                    if ($day === 'saturday') $color = '#f97316';

                    $datesToInsert[] = [
                        'date'       => $date->toDateString(),
                        'title'      => $title,
                        'type'       => 'weekend',
                        'is_default' => 1,
                        'color'      => $color,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert only missing dates (because unique(date))
        foreach ($datesToInsert as $row) {
            Holiday::firstOrCreate(
                ['date' => $row['date']],
                $row
            );
        }
    });

    return back()->with('success', "Default holidays added for year {$year}.");
}

}
