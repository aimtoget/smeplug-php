<?php

namespace SmePlug;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use SmePlug\Exceptions\RequestException;
use SmePlug\Exceptions\ResponseException;

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
     * @return object Response data
     */
    public function purchaseDataPlan(string $network_id, string $plan_id, string $phone): object
    {
        $payload = array(
            'network_id' => $network_id,
            'plan_id' => $plan_id,
            'phone' => $phone
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
     * @return object Response Object
     */
    public function purchaseAirtime(string $network_id, float $amount, string $phone): object
    {
        $payload = array(
            'network_id' => $network_id,
            'amount' => $amount,
            'phone' => $phone
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
     * @return object
     */
    public function bankTransfer(string $bank_code, string $account_number, float $amount, ?string $description = null): object
    {
        $payload = array(
            'bank_code' => $bank_code,
            'account_number' => $account_number,
            'amount' => $amount,
            'description' => $description
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
    private function request(string $method, string $endpoint, ?array $payload = null)
    {
        $uri = $this->base_uri . $endpoint;
        $client = new Client([
            'timeout' => 50000,
            RequestOptions::HEADERS => array(
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->key
            )
        ]);

        try {
            $response = $client->request($method, $uri, [
                RequestOptions::JSON => $payload
            ]);
        } catch (Exception $e) {
            throw new RequestException('Request failed');
        }

        $content = $response->getBody()->getContents();
        $data = json_decode($content);

        if(!$data->status) {
            throw new ResponseException($data->msg);
        }
        
        return $data;
    }
}