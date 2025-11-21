<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\WebController;
use Boctulus\Simplerest\Core\Libs\ApiClient;

class XeniTestController extends WebController
{
    private const API_KEY = '96989ee3-5c9c-4557-851c-40d292ab4319';
    private const SECRET = 'M$72tYWz$3ZJJ71';
    private const BASE_URL = 'https://uat.travelapi.ai';

    private $token;
    private $hotelId;
    private $rateKey;
    private $bookingId;

    /**
     * UI-Level Explanation:
     * This is the main entry point for testing the Xeni API integration.
     * A UI could have a "Run All Tests" button that triggers this action.
     * It sequentially calls all the test methods and displays their results.
     */
    public function index()
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
     * This test authenticates with the Xeni API to get a session token.
     * In a real UI, this would happen automatically in the background
     * before making any other API calls. The user wouldn't see it.
     */
    public function test_auth()
    {
        echo "<h2>Testing Authentication...</h2>";

        $client = new ApiClient(self::BASE_URL);
        
        $payload = [
            'key' => self::API_KEY,
            'secret' => self::SECRET
        ];

        $client->post('/auth/login', $payload);

        if ($client->status() == 200 && !empty($client->data()['token'])) {
            $this->token = $client->data()['token'];
            echo "<p>Authentication successful. Token received.</p>";
            return true;
        } else {
            echo "<p>Authentication failed.</p>";
            dd($client->getResponse());
            return false;
        }
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
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/search', $payload);

        if ($client->status() == 200) {
            $data = $client->data();
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
            dd($client->getResponse());
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
        if (empty($this->hotelId)) {
            echo "<p>Skipping rates test: No Hotel ID available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        $payload = [
            "hotelId" => $this->hotelId
        ];

        $client->setHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/rates', $payload);

        if ($client->status() == 200) {
            $data = $client->data();
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
            dd($client->getResponse());
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
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])
        ->post('/hotels/booking', $payload);

        if ($client->status() == 200) {
            $data = $client->data();
            if (!empty($data['booking']['id'])) {
                $this->bookingId = $data['booking']['id'];
                echo "<p>Hotel booking successful.</p>";
                echo "<p>Booking ID: " . $this->bookingId . ". This will be used for cancellation.</p>";
                return true;
            } else {
                echo "<p>Hotel booking seems to have succeeded but no Booking ID was returned.</p>";
                dd($data);
                return false;
            }
        } else {
            echo "<p>Hotel booking failed.</p>";
            dd($client->getResponse());
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
        if (empty($this->bookingId)) {
            echo "<p>Skipping cancellation test: No Booking ID available.</p>";
            return false;
        }

        $client = new ApiClient(self::BASE_URL);

        $payload = [
            "bookingId" => $this->bookingId
        ];

        $client->setHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])
        ->delete('/hotels/booking/cancel', $payload);

        if ($client->status() == 200) {
            echo "<p>Booking cancellation successful.</p>";
            return true;
        } else {
            echo "<p>Booking cancellation failed.</p>";
            dd($client->getResponse());
            return false;
        }
    }
}
