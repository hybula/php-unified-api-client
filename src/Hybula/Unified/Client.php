<?php

declare(strict_types=1);

namespace Hybula\Unified;

class Client
{
    private string $apiUrl;
    private string $apiProduct;
    private string $apiDomain;
    private string $apiKey;
    private string $apiToken;
    private bool $curlValidateSsl = true;

    private function checkApiConfig(): bool
    {
        if (!isset($this->apiUrl) || !isset($this->apiProduct) || !isset($this->apiDomain) || !isset($this->apiKey) || !isset($this->apiToken)) {
            throw new \Exception('Not all API params are set.');
        }
        return true;
    }

    public function __construct(string $apiUrl = 'https://uni.hybula.com/v1')
    {
        $this->apiUrl = $apiUrl;
    }

    public function setAuthCredentials(string $apiKey, string $apiToken): void
    {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
    }

    public function setApiProduct(string $apiProduct): void
    {
        $this->apiProduct = $apiProduct;
    }

    public function setDomain(string $apiDomain): void
    {
        $this->apiDomain = $apiDomain;
    }

    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    public function apiCall(string $httpMethod, string $endpoint, array $payload = []): array
    {
        $this->checkApiConfig();
        if (!in_array($httpMethod, ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'])) {
            throw new \Exception('Unsupported HTTP method.');
        }
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, [
            CURLOPT_URL => $this->apiUrl . '/' . $this->apiProduct . '/' . $this->apiDomain . '/' . $endpoint,
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
        if (!$this->curlValidateSsl) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }
}
