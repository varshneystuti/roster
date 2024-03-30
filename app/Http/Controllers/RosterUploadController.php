<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use App\Http\Requests\UploadRosterRequest;

class RosterUploadController extends Controller
{
   
public function upload(UploadRosterRequest $request)
{
    try {
        // Store the uploaded file in the "rosters" folder
        $path = $request->file('roster_file')->store('rosters');

        // Determine the file type and parse accordingly
        $fileType = $request->file('roster_file')->getClientOriginalExtension();

        switch ($fileType) {
            case 'pdf':
                $this->parsePdf(storage_path("app/{$path}"));
                break;
            case 'html':
                $this->parseHtml(storage_path("app/{$path}"));
                break;
            default:
                // Handle unexpected file type if necessary
                throw new \Exception("Unsupported file type: {$fileType}");
        }

        return response()->json(['message' => 'File uploaded and parsed successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred during the upload and parsing process.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    

    protected function parsePdf($filePath)
    {
        // Logic to pdf parsing
    }
    
    protected function parseHtml($filePath)
    {
        // Load the HTML content
        $htmlContent = file_get_contents($filePath);
        $crawler = new Crawler($htmlContent);
        $events = [];

        list($startDate, $endDate) = $this->findReportPeriod($crawler);
         
        $crawler->filter('table.activityTableStyle > tbody > tr')->each(function (Crawler $node, $i) use (&$events, $startDate, $endDate) {
    if ($i === 0) {
        return;
    }
            if ($node->filter('.activitytablerow-date')->count() > 0) {
                $date = $node->filter('.activitytablerow-date')->text();
                $dayNumber = intval(trim(substr($date, strpos($date, ' ') + 1))); // Extract day number 10 from "Mon 10"

                $eventDate = $startDate->copy();

// Find the correct date for the event
// Assuming the event's month is the same as the report's start month
$eventDate->day($dayNumber);

// If the day number is less than the start date's day, and the report spans into the next month,
// adjust the month. This simple check assumes reports don't span more than one month.
if ($dayNumber < $startDate->day && $startDate->month != $endDate->month) {
    $eventDate->addMonth();
}

// Now, $eventDate is the correct date for the event
// Format it as needed
$correctDateFormat = $eventDate->format('d-m-Y'); // e.g., "10-01-2022"
                $activity = $node->filter('.activitytablerow-activity')->text();
                $from = $node->filter('.activitytablerow-fromstn')->text();
                $to = $node->filter('.activitytablerow-tostn')->text();
                $stdZulu = $node->filter('.activitytablerow-stdutc')->text();
                $staZulu = $node->filter('.activitytablerow-stautc')->text();
                $flightNumber = null;
    
                // Determine if the activity is a flight based on the flight number pattern
                if (preg_match('/[A-Z]{2}\d+/', $activity, $matches)) {
                    $flightNumber = $matches[0];
                }
    
                // Store or process your event data here
                // Example: create an event array and later use it to insert into the database
                $eventData = [
                    'date' => $correctDateFormat,
                    'activity' => $activity,
                    'from' => $from,
                    'to' => $to,
                    'start_time' => $stdZulu,
                    'end_time' => $staZulu,
                    'flight_number' => $flightNumber,
                ];
    
                // Add the event data to the events array
                $events[] = $eventData;
            }
        });
    
        // After parsing all rows, insert the events into the database
        foreach ($events as $event) {
            // Example of creating a new event in the database
            Event::create([
                'type' => $event['activity'],
                'start_time' => $this->convertToDateTime($event['date'], $event['start_time']),
                'end_time' => $this->convertToDateTime($event['date'], $event['end_time']),
                'location' => $event['from'],
                // Include other fields as per your database schema
                'flight_number' => $event['flight_number'],
            ]);
        }
    }
    
    /**
     * Helper function to combine date and time and convert to a DateTime object or string.
     * Adjust this function as needed based on your requirements.
     */
    protected function convertToDateTime($date, $time)
    {
        // Example: Convert date and time to a format suitable for your database
        // This is a placeholder implementation. You'll need to adjust it based on the actual date and time format in the HTML.
        return $date . ' ' . $time;
    }

    protected function findReportPeriod(Crawler $crawler)
    {
        // Adjust the selector to target the specific HTML structure
        $reportPeriodText = $crawler->filter('.row.printOnly b')->text();
    
        // Regex to extract the dates, using the known format
        preg_match('/(\d{1,2}[A-Za-z]{3}\d{2})\s+to\s+(\d{1,2}[A-Za-z]{3}\d{2})/', $reportPeriodText, $matches);
    
        if (count($matches) >= 3) {
            $startDate = Carbon::createFromFormat('dMy', $matches[1]);
            $endDate = Carbon::createFromFormat('dMy', $matches[2]);
            return [$startDate, $endDate];
        }
    
        return [null, null];
    }

 }

