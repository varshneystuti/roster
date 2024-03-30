<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\YourController; // Replace with the actual name of your controller
use Tests\TestCase;

class UploadTest extends TestCase
{
    /** @test */
    public function it_processes_html_files_correctly()
    {
        Storage::fake('rosters');

  // Assuming your file is located at public/sample.html
  $filePath = public_path('roster.html');

  // Create an UploadedFile instance for the real file
  $uploadedFile = new UploadedFile(
      $filePath,
      'roster.html', // Original name of the file
      'text/html', // MIME type
      null, // Error
      true // Test mode, this flags the file as being uploaded.
  );
        // Simulate POST request to upload endpoint

        $response = $this->post('/api/upload-roster', [
            'roster_file' => $uploadedFile,
        ], ['Accept' => 'application/json']);

        // Assert the file was stored
        Storage::disk('rosters')->allFiles();

        // Assert response
        $response->assertOk()
                 ->assertJson(['message' => 'File uploaded and parsed successfully']);

        // Additional assertions to verify HTML parsing logic can be placed here
        // This depends on how your parseHtml method works and what side effects it has (e.g., database records creation)
    }
}
