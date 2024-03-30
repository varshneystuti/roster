# Running the Laravel Project
First, ensure you have Composer installed on your system. Then, open your terminal and navigate to your project directory. Run the following commands:

# Install dependencies
composer install

# Copy the example env file and make the necessary configuration adjustments
cp .env.example .env

# Generate a new application key
php artisan key:generate

# Run migrations (ensure your database connection settings in .env are correct)
php artisan migrate

# Start the development server
php artisan serve

This will start a development server at http://127.0.0.1:8000 by default.

# Running Test Cases
To run your test cases, ensure you are in the root directory of your Laravel project. Use the following command to execute all tests:

# Run all tests
./vendor/bin/phpunit

# Or use the Laravel artisan command
php artisan test

To run a specific test, you can specify the path to the test file:

./vendor/bin/phpunit tests/Feature/YourTestFile.php


### Download Postman Collection

To test the API endpoints with Postman, download the Postman collection file from the `public` directory of the project. The file is named `Roster.postman_collection.json`. You can import this file directly into Postman to access all the configured requests.


# API URLs with Curl Calls
Based on the Postman collection provided, here are the curl commands for your API endpoints:

1. Upload File

curl --location --request POST 'http://127.0.0.1:8000/api/upload-roster' \
--header 'Accept: application/json' \
--form 'roster_file=@"/path/to/your/file.html"'
Replace "/path/to/your/file.html" with the actual path to your HTML file.

2. Get Flights between Two Dates

curl --location --request GET 'http://127.0.0.1:8000/api/events?start_date=2022-01-10&end_date=2022-01-31' \
--header 'Accept: application/json'

3. Get Flights with Location Code

curl --location --request GET 'http://127.0.0.1:8000/api/flights/from-location?location=CPH' \
--header 'Accept: application/json'

4. Flights On Stand By

curl --location --request GET 'http://127.0.0.1:8000/api/flights/next-week-sby'


5. Get Flights Of Next Week

curl --location --request GET 'http://127.0.0.1:8000/api/flights/next-week'

Additional Notes
Ensure Laravel and all dependencies are installed and configured correctly before running the project or test cases.
The curl commands provided assume your Laravel development server is running at http://127.0.0.1:8000. Adjust the URLs as needed based on your actual development or production environment.
The file upload curl command requires an actual file path. Ensure the file exists at the specified location on your system before running the command.





