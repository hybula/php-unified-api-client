<?php
/**
 * =========================================
 * =========================================
 * **   _  ___   _____ _   _ _      _     **
 * **  | || \ \ / / _ ) | | | |    /_\    **
 * **  | __ |\ V /| _ \ |_| | |__ / _ \   **
 * **  |_||_| |_| |___/\___/|____/_/ \_\  **
 * **                                     **
 * =========================================
 * =========================================
 *
 * Unified API Client (PHP)
 *
 * @package Hybula\Unified
 * @author Hybula Development Team <development@hybula.com>
 * @version 0.0.1
 * @copyright Hybula B.V.
 * @license MPL-2.0 License
 * @see https://github.com/hybula/php-unified-api-client
 */

declare(strict_types=1);

namespace Hybula\Unified;

class Client
{
    private string $apiUrl;
    private string $apiCore;
    private string $apiKey;
    private string $apiToken;
    private bool $skipSslValidation = false;

    /**
     * The constructor defines the API server to use, by default the current one is used.
     *
     * @param string $apiUrl
     */
    public function __construct(string $apiUrl = 'https://api.hybula.com/v1')
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Checks whether all API related configs are set.
     *
     * @return bool
     * @throws \Exception
     */
    private function checkApiConfig(): bool
    {
        if (!isset($this->apiUrl) || !isset($this->apiCore) || !isset($this->apiKey) || !isset($this->apiToken)) {
            throw new \Exception('Not all API params are set.');
        }
        return true;
    }

    /**
     * Sets authentication credentials to use for API calls.
     *
     * @param string $apiKey
     * @param string $apiToken
     */
    public function setAuthCredentials(string $apiKey, string $apiToken): void
    {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
    }

    /**
     * Tell cURL to skip SSL validation, useful when developing locally.
     *
     * @param bool $skipSslValidation
     */
    public function skipSslValidation(bool $skipSslValidation = false): void
    {
        $this->skipSslValidation = $skipSslValidation;
    }

    /**
     * Sets the API core to use, e.g. capsule/pyramid/antidote.
     *
     * @param string $apiCore
     */
    public function setApiCore(string $apiCore): void
    {
        $this->apiCore = $apiCore;
    }

    /**
     * Check whether a successful API-call is possible, so sends a PING and returns a PONG on success.
     *
     * @return bool
     * @throws \Exception
     */
    public function pingPong(): bool
    {
        $apiCall = $this->apiCall('GET', 'ping');
        if (isset($apiCall['results']) && $apiCall['results'] == 'pong') {
            return true;
        }
        return false;
    }

    /**
     * @param string $httpMethod The HTTP method to use.
     * @param string $endpoint The endpoint to call, this may/must include the domain.
     * @param array $payload The body content of the request, also called payload.
     * @return array
     * @throws \Exception
     */
    public function apiCall(string $httpMethod, string $endpoint, array $payload = []): array
    {
        $this->checkApiConfig();
        if (!in_array($httpMethod, ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'])) {
            throw new \Exception('Unsupported HTTP method.');
        }
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, [
            CURLOPT_URL => $this->apiUrl . '/' . $this->apiCore . '/' . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiToken)
            ],
        ]);
        if ($this->skipSslValidation) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($curlHandle);
        if (strpos($response, '"status"') !== false) { // Be sure we have the expected output before json_decoding();
            return json_decode($response, true);
        }
        curl_close($curlHandle);
        return [];
    }
}
