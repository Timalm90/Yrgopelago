<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CentralBankService
{
    private Client $client;
    private string $hotelUser;
    private string $apiKey;

    public function __construct()
    {
        $this->hotelUser = $_ENV['HOTEL_USER'] ?? '';
        $this->apiKey    = $_ENV['API_KEY'] ?? '';

        if (!$this->hotelUser || !$this->apiKey) {
            throw new RuntimeException('CentralBank credentials missing');
        }

        $this->client = new Client([
            'base_uri' => 'https://www.yrgopelag.se/centralbank/',
            'timeout'  => 5,
        ]);
    }

    /**
     * Validate transfer code
     * IMPORTANT:
     *  - NO user
     *  - NO api_key
     *  - EXACT amount
     */
    public function validateTransfer(string $transferCode, float $amount): bool
    {
        try {
            $response = $this->client->post('transferCode', [
                'json' => [
                    'transferCode' => $transferCode,
                    'totalCost'    => round($amount, 2),
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return isset($data['status']) && $data['status'] === 'success';
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Deposit (consumes transfer code)
     */
    public function deposit(string $transferCode): bool
    {
        try {
            $response = $this->client->post('deposit', [
                'json' => [
                    'user'         => $this->hotelUser,
                    'api_key'      => $this->apiKey,
                    'transferCode' => $transferCode,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return isset($data['status']) && $data['status'] === 'success';
        } catch (RequestException $e) {
            return false;
        }
    }
}
