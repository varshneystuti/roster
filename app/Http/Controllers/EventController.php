<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventDateRangeRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Event;
use App\Http\Requests\FlightLocationRequest;

class EventController extends Controller
{
    public function getEventsBetweenDates(EventDateRangeRequest $request)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
    
        try {
            $events = Event::query()
                ->whereRaw("datetime(substr(start_time, 7, 4) || '-' || substr(start_time, 4, 2) || '-' || substr(start_time, 1, 2)) >= ?", [$startDate->format('Y-m-d')])
                ->whereRaw("datetime(substr(end_time, 7, 4) || '-' || substr(end_time, 4, 2) || '-' || substr(end_time, 1, 2)) <= ?", [$endDate->format('Y-m-d')])
                ->get();
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching events.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return response()->json($events);
    }

    public function getFlightsForNextWeekStandBy(Request $request)
    {
        try {
            // Assuming the current date is January 14, 2022
            $currentDate = Carbon::createFromDate(2022, 1, 14);
    
            // Calculate the start and end dates for the next week
            $nextWeekStartDate = $currentDate->copy()->addDay(); // January 15, 2022
            $nextWeekEndDate = $currentDate->copy()->addWeek()->endOfDay(); // January 21, 2022, 23:59:59
    
            // Format the dates for comparison
            $dbFormatStartDate = $nextWeekStartDate->format('Y-m-d');
            $dbFormatEndDate = $nextWeekEndDate->format('Y-m-d');
    
            // Query for flights marked as 'SBY' within the next week
            $flights = Event::where('type', 'SBY')
                            ->whereRaw("substr(start_time, 7, 4) || '-' || substr(start_time, 4, 2) || '-' || substr(start_time, 1, 2) >= ?", [$dbFormatStartDate])
                            ->whereRaw("substr(end_time, 7, 4) || '-' || substr(end_time, 4, 2) || '-' || substr(end_time, 1, 2) <= ?", [$dbFormatEndDate])
                            ->get();
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => 'An error occurred while fetching flights.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return response()->json($flights);
    }

    public function getFlightsForNextWeek(Request $request)
    {
        try {
            // Assuming the current date is January 14, 2022
            $currentDate = Carbon::createFromDate(2022, 1, 14);
    
            // Calculate the start and end dates for the next week
            $nextWeekStartDate = $currentDate->copy()->addDay(); // January 15, 2022
            $nextWeekEndDate = $currentDate->copy()->addWeek()->endOfDay(); // January 21, 2022, 23:59:59
    
            // Format the dates for comparison
            $dbFormatStartDate = $nextWeekStartDate->format('Y-m-d');
            $dbFormatEndDate = $nextWeekEndDate->format('Y-m-d');
    
            // Query for events within the next week, adjusting for the custom date format
            $flights = Event::whereRaw("substr(start_time, 7, 4) || '-' || substr(start_time, 4, 2) || '-' || substr(start_time, 1, 2) >= ?", [$dbFormatStartDate])
                            ->whereRaw("substr(end_time, 7, 4) || '-' || substr(end_time, 4, 2) || '-' || substr(end_time, 1, 2) <= ?", [$dbFormatEndDate])
                            ->get();
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            return response()->json(['error' => 'An error occurred while fetching flights for the next week.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return response()->json($flights);
    }

       

  
public function getFlightsFromLocation(FlightLocationRequest $request)
{
    try {
        // Extract the location from the validated request
        $location = $request->location;

        // Query for flights starting from the given location
        $flights = Event::where('location', $location)
                        ->get();
    } catch (\Exception $e) {
        // Log the exception or handle it as needed
        return response()->json(['error' => 'An error occurred while fetching flights from the specified location.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return response()->json($flights);
}
}
