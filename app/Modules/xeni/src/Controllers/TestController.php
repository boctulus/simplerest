<?php

namespace Boctulus\Simplerest\Modules\xeni\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class TestController extends Controller
{
    private const API_KEY = '96989ee3-5c9c-4557-851c-40d292ab4319';
    private const SECRET = 'M$72tYWz$3ZJJ71';
    private const BASE_URL = 'https://uat.travelapi.ai';

    private $signature;
    private $timestamp;
    private $hotelId;
    private $rateKey;
    private $bookingId;

    function __construct() { parent::__construct(); }

    function index()
    {
        echo "<h1>Xeni API Tests</h1>";

        if ($this->test_auth()) {
            if ($this->test_hotel_search()){
                if($this->test_hotel_rates()){
                    if($this->test_hotel_booking()){
                        $this->test_booking_cancellation();
                    }
                }
            }
        }

        echo "<h2>All tests completed.</h2>";
    }

    /**
     * UI-Level Explanation:
     * This is the main entry point for testing the Xeni API integration.
     * A UI could have a "Run All Tests" button that triggers this action.
     * It sequentially calls all the test methods and displays their results.
     */
    public function run_tests()
    {
        $this->index();
    }

    /**
     * UI-Level Explanation:
     * This test authenticates with the Xeni API to get a session signature.
     * In a real UI, this would happen automatically in the background
     * before making any other API calls. The user wouldn't see it.
     */
    public function test_auth()
    {
        echo "<h2>Testing Authentication...</h2>";
        echo "<p>Attempting to generate a signature from Xeni API...</p>";

        $client = new ApiClient(self::BASE_URL);
        
        $this->timestamp = time();

        $payload = [
            'api_key' => self::API_KEY,
            'secret' => self::SECRET,
            'timestamp' => $this->timestamp
        ];

        $client->setHeaders([
            'Content-Type' => 'application/json'
        ])->post('/identity/v2/auth/generate', $payload);

        $response = $client->getResponse();

        if ($client->status() == 200 && !empty($response['data']['signature'])) {
            $this->signature = $response['data']['signature'];
            echo "<p>✓ Authentication successful. Signature received.</p>";
            return true;
        } else {
            echo "<p>✗ Authentication failed. HTTP Code: " . $client->status() . "</p>";
            echo "<p>Response: ";
            var_dump($response);
            echo "</p>";
            echo "<p>Note: If consistently failing, please verify API credentials and endpoint availability.</p>";
        }

        return false;
    }

    private function getAuthHeader(): string
    {
        return sprintf(
            'XN api_key=%s,signature=%s,timestamp=%s',
            self::API_KEY,
            $this->signature,
            $this->timestamp
        );
    }

    /**
     * UI-Level Explanation:
     * This test searches for hotels based on specific criteria.
     * A UI would have a form where the user inputs destination, dates, and number of guests.
     * Submitting the form would trigger this API call. The results would be displayed as a list of hotels.
     */
    public function test_hotel_search()
    {
        echo "<h2>Testing Hotel Search...</h2>";

        if (empty($this->signature)) {
            echo "<p>Skipping hotel search: No authentication signature available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        // Dates must be within a 3-month window from today
        $checkin = date('Y-m-d', strtotime('+1 month'));
        $checkout = date('Y-m-d', strtotime('+1 month + 5 days'));

        $payload = [
            "stay" => [
                "checkIn" => $checkin,
                "checkOut" => $checkout
            ],
            "occupancies" => [
                [
                    "adults" => 2
                ]
            ],
            "destination" => [
                "name" => "london",
                "country" => "GB"
            ],
            "agent" => [
                "currency" => "USD"
            ]
        ];

        $client->setHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/search', $payload);

        if ($client->status() == 200) {
            $response = $client->getResponse();
            $data = $response['data'];
            if (!empty($data)) {
                $this->hotelId = $data[0]['hotelId'];
                echo "<p>Hotel search successful. Found " . count($data) . " hotel options.</p>";
                echo "<p>Using Hotel ID: " . $this->hotelId . " for the next step.</p>";
                return true;
            } else {
                echo "<p>Hotel search successful, but no hotels returned.</p>";
                return false;
            }
        } else {
            echo "<p>Hotel search failed.</p>";
            var_dump($client->getResponse());
            return false;
        }
    }

    /**
     * UI-Level Explanation:
     * After a user selects a hotel from the search results, the UI calls this endpoint to get available room types and their rates.
     * This would typically be triggered by clicking a "View Rooms" or "Check Rates" button on a specific hotel.
     */
    public function test_hotel_rates()
    {
        echo "<h2>Testing Hotel Rates...</h2>";
        if (empty($this->signature)) {
            echo "<p>Skipping rates test: No authentication signature available.</p>";
            return false;
        }

        if (empty($this->hotelId)) {
            echo "<p>Skipping rates test: No Hotel ID available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        $payload = [
            "hotelId" => $this->hotelId
        ];

        $client->setHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/rates', $payload);

        if ($client->status() == 200) {
            $response = $client->getResponse();
            $data = $response['data'];
            if (!empty($data['rooms'])) {
                // Find a rate that is less than $100 to comply with test rules
                foreach ($data['rooms'] as $room) {
                    foreach ($room['rates'] as $rate) {
                        if ($rate['net'] < 100) {
                            $this->rateKey = $rate['rateKey'];
                            echo "<p>Hotel rates fetched successfully.</p>";
                            echo "<p>Found a valid rate less than $100. Using Rate Key: " . $this->rateKey . " for booking.</p>";
                            return true;
                        }
                    }
                }
                echo "<p>Hotel rates fetched, but no rooms available under $100.</p>";
                return false;
            } else {
                echo "<p>Hotel rates fetched, but no rooms available.</p>";
                return false;
            }
        } else {
            echo "<p>Hotel rates fetch failed.</p>";
            var_dump($client->getResponse());
            return false;
        }
    }

    /**
     * UI-Level Explanation:
     * This is the final step in the booking process. After the user has reviewed the rates and decided to book,
     * the UI would collect guest information and call this endpoint. This action confirms the reservation.
     */
    public function test_hotel_booking()
    {
        echo "<h2>Testing Hotel Booking...</h2>";
        if (empty($this->signature)) {
            echo "<p>Skipping booking test: No authentication signature available.</p>";
            return false;
        }

        if (empty($this->rateKey)) {
            echo "<p>Skipping booking test: No Rate Key available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        $payload = [
            "rateKey" => $this->rateKey,
            "holder" => [
                "name" => "John",
                "surname" => "Doe"
            ],
            "rooms" => [
                [
                    "paxes" => [
                        [
                            "type" => "AD",
                            "name" => "John",
                            "surname" => "Doe"
                        ],
                         [
                            "type" => "AD",
                            "name" => "Jane",
                            "surname" => "Doe"
                        ]
                    ]
                ]
            ]
        ];

        $client->setHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/booking', $payload);

        if ($client->status() == 200) {
            $response = $client->getResponse();
            $data = $response['data'];
            if (!empty($data['booking']['id'])) {
                $this->bookingId = $data['booking']['id'];
                echo "<p>Hotel booking successful.</p>";
                echo "<p>Booking ID: " . $this->bookingId . ". This will be used for cancellation.</p>";
                return true;
            } else {
                echo "<p>Hotel booking seems to have succeeded but no Booking ID was returned.</p>";
                var_dump($data);
                return false;
            }
        } else {
            echo "<p>Hotel booking failed.</p>";
            var_dump($client->getResponse());
            return false;
        }
    }

    /**
     * UI-Level Explanation:
     * This allows a user to cancel a booking they have made.
     * In a UI, this would be a "Cancel Booking" button on the booking details page.
     * This action is critical for testing to avoid leaving active reservations.
     */
    public function test_booking_cancellation()
    {
        echo "<h2>Testing Booking Cancellation...</h2>";
        if (empty($this->signature)) {
            echo "<p>Skipping cancellation test: No authentication signature available.</p>";
            return false;
        }

        if (empty($this->bookingId)) {
            echo "<p>Skipping cancellation test: No Booking ID available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        $payload = [
            "bookingId" => $this->bookingId
        ];

        $client->setHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json'
        ])
        ->delete('/hotels/booking/cancel', $payload);

        if ($client->status() == 200) {
            echo "<p>Booking cancellation successful.</p>";
            return true;
        } else {
            echo "<p>Booking cancellation failed.</p>";
            var_dump($client->getResponse());
            return false;
        }
    }
}

