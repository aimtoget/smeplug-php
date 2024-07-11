<?php

namespace SmePlug;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use SmePlug\Exceptions\RequestException;
use SmePlug\Exceptions\ResponseException;
use SmePlug\Exceptions\TimeoutException;

class SmePlug
{
    private $base_uri = 'https://smeplug.ng/api/v1';

    /**
     * API Key
     *
     * @var string
     */
    private $key;

    public function __construct(string $private_key)
    {
        $this->key = $private_key;
    }

    /**
     * Retrieve all networks
     *
     * @return object
     */
    public function getNetworks(): object
    {
        return $this->request('GET', '/networks')->networks;
    }

    /**
     * Get all data plans
     *
     * @return object
     */
    public function getDataPlans(): object
    {
        return $this->request('GET', '/data/plans')->data;
    }

    /**
     * Purchase Data Plan
     *
     * @param string $network_id
     * @param string $plan_id
     * @param string $phone
     * @param string|null $customer_reference
     * @param bool $async
     * @return object Response data
     */
    public function purchaseDataPlan(string $network_id, string $plan_id, string $phone, ?string $customer_reference = null, bool $async = false): object
    {
        $payload = array(
            'network_id' => $network_id,
            'plan_id' => $plan_id,
            'phone' => $phone,
            'customer_reference' => $customer_reference,
            'async' => $async
        );

        $response = $this->request('POST', '/data/purchase', $payload);
        return $response->data;
    }

    /**
     * Purchase airtime
     *
     * @param string $network_id
     * @param float $amount
     * @param string $phone
     * @param string|null $customer_reference
     * 
     * @return object Response Object
     */
    public function purchaseAirtime(string $network_id, float $amount, string $phone, ?string $customer_reference = null): object
    {
        $payload = array(
            'network_id' => $network_id,
            'amount' => $amount,
            'phone' => $phone,
            'customer_reference' => $customer_reference
        );

        $response = $this->request('POST', '/airtime/purchase', $payload);
        return $response->data;
    }

    /**
     * Get transfer bank list
     *
     * @return array
     */
    public function getTransferBanksList(): array
    {
        return $this->request('GET', '/transfer/banks')->banks;
    }

    /**
     * Resolve account name
     *
     * @param string $bank_code
     * @param string $account_number
     * @return string
     */
    public function resolveAccountDetails(string $bank_code, string $account_number): string
    {
        $payload = array(
            'bank_code' => $bank_code,
            'account_number' => $account_number
        );

        $response = $this->request('POST', '/transfer/resolveaccount', $payload);
        return $response->name;
    }

    /**
     * Initiate a bank transfer
     *
     * @param string $bank_code
     * @param string $account_number
     * @param float $amount
     * @param string|null $description
     * @param string|null $customer_reference
     * 
     * @return object
     */
    public function bankTransfer(string $bank_code, string $account_number, float $amount, ?string $description = null, ?string $customer_reference = null): object
    {
        $payload = array(
            'bank_code' => $bank_code,
            'account_number' => $account_number,
            'amount' => $amount,
            'description' => $description,
            'customer_reference' => $customer_reference
        );

        $response = $this->request('POST', '/transfer/send', $payload);
        return $response->data;
    }

    /**
     * Make request
     *
     * @param string $method
     * @param string $endpoint
     * @param array|null $payload
     * @return object
     */
    private function request(string $method, string $endpoint, array $payload = [])
    {
        $uri = $this->base_uri . $endpoint;
        $is_get = strtoupper($method) === 'GET';

        if ($is_get && count($payload)) {
            $uri .= '?' . http_build_query($payload);
        }

        $ch = curl_init();
        $opts = array(
            CURLOPT_URL => $uri,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_CONNECTTIMEOUT => 50,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
                'Content-type: application/json',
                'Authorization: Bearer ' . $this->key,
                'Accept: */*',
            )
        );

        if (!$is_get) {
            $opts[CURLOPT_CUSTOMREQUEST] = $method;
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error_no = curl_errno($ch);
        curl_close($ch);

        if ($error_no == 28) {
            throw new TimeoutException('Service timed out');
        }

        if ($status_code >= 200 && $status_code <= 299) {
            return json_decode($data);
        }

        throw new ResponseException($data);
    }
}
