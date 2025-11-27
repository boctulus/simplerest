<?php

namespace Boctulus\Simplerest\Modules\Xeni\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class V1TestController extends Controller
{
    private const API_KEY = '96989ee3-5c9c-4557-851c-40d292ab4319';
    private const SECRET = 'M$72tYWz$3ZJJ71';
    private const BASE_URL = 'https://uat.travelapi.ai';

    private $signature;
    private $timestamp;

    function __construct() { parent::__construct(); }

    /**
     * API v1 Authentication Test Endpoint
     * Accessible at /xeni/v1/test
     */
    public function index()
    {
        // Set content type to JSON for API response
        header('Content-Type: application/json');

        $result = [
            'status' => 'success',
            'message' => 'Xeni API v1 authentication test endpoint',
            'timestamp' => date('Y-m-d H:i:s'),
            'tests' => []
        ];

        // First authenticate
        $auth_result = $this->api_auth();
        $result['tests']['authentication'] = $auth_result;

        // If authentication successful, try an authenticated API call
        if ($auth_result['status'] === 'success') {
            $result['signature'] = $auth_result['signature'];
            $result['timestamp'] = $auth_result['timestamp'];

            $search_result = $this->test_hotel_search();
            $result['tests']['hotel_search'] = $search_result;

            if ($search_result['status'] === 'success') {
                $rate_result = $this->test_hotel_rates();
                $result['tests']['hotel_rates'] = $rate_result;
            }
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * Test authentication with Xeni API v1 - improved version
     */
    public function api_auth()
    {
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
        $status_code = $client->status();

        if ($status_code == 200 && !empty($response['data']['signature'])) {
            $this->signature = $response['data']['signature'];
            return [
                'status' => 'success',
                'message' => 'Authentication successful',
                'signature' => $this->signature,
                'timestamp' => $this->timestamp,
                'http_code' => $status_code
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Authentication failed - ' . ($response['data']['message'] ?? 'No signature received'),
                'http_code' => $status_code,
                'response' => $response,
                'credentials_used' => [
                    'api_key' => substr(self::API_KEY, 0, 4) . '...' . substr(self::API_KEY, -4),
                    'secret' => substr(self::SECRET, 0, 2) . '...' . substr(self::SECRET, -2)
                ]
            ];
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
        if (empty($this->signature)) {
            return [
                'status' => 'error',
                'message' => 'Skipping hotel search: No authentication signature available.'
            ];
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
                return [
                    'status' => 'success',
                    'message' => 'Hotel search successful',
                    'hotel_count' => count($data),
                    'first_hotel_id' => $data[0]['hotelId'] ?? null
                ];
            } else {
                return [
                    'status' => 'warning',
                    'message' => 'Hotel search successful, but no hotels returned.'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Hotel search failed',
                'http_code' => $client->status(),
                'response' => $client->getResponse()
            ];
        }
    }

    /**
     * Test hotel rates API call
     */
    public function test_hotel_rates()
    {
        if (empty($this->signature)) {
            return [
                'status' => 'error',
                'message' => 'Skipping rates test: No authentication signature available.'
            ];
        }

        $client = new ApiClient(self::BASE_URL);

        // We need a specific hotelId for this test, using a placeholder or trying with a specific known hotel
        // For now, we'll skip if no hotelId from search
        return [
            'status' => 'skipped',
            'message' => 'Hotel rates test skipped - requires specific hotel ID from search results'
        ];
    }

    /**
     * Generate the authentication header for making API calls after authentication
     */
    private function getAuthHeader(): string
    {
        if (empty($this->signature) || empty($this->timestamp)) {
            // This shouldn't happen if called after successful auth
            return '';
        }

        return sprintf(
            'XN api_key=%s,signature=%s,timestamp=%s',
            self::API_KEY,
            $this->signature,
            $this->timestamp
        );
    }
}