<?php
/**
* Copyright 2020 COQUARD Cyrille
*
* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
*
* 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
*
* 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


namespace CoquardCyrilleFreelance\SCBPaymentAPI;

use DateTime;
use DateTimeZone;
use CoquardCyrilleFreelance\SCBPaymentAPI\Exceptions\SCBPaymentAPIException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use stdClass;

/**
 * Class API that wrap SCB API to prevent strong dependencies on it
 */
class API
{
    /**
     * @var string base URL for all requests
     */
    protected $baseURL = '';
    /**
     * @var string token for requests
     */
    protected $token = '';
    /**
     * @var string language in payload responses
     */
    protected $language = '';
    /**
     * @var string ID from the application
     */
    protected $appId = '';
    /**
     * @var string ID from the merchant
     */
    protected $merchant = '';
    /**
     * @var string ID from the terminal
     */
    protected $terminal = '';
    /**
     * @var string ID from the biller
     */
    protected $biller = '';
    /**
     * @var string Reference prefix from the biller
     */
    protected $prefix = '';

    /**
     * HTTP Client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * API constructor.
     * @param ClientInterface $client HTTP Client.
     * @param string $appId ID from the application
     * @param string $appSecret Secret ID from the application
     * @param string $merchant ID from the merchant
     * @param string $terminal ID from the terminal
     * @param string $biller ID from the biller
     * @param string $prefix Reference prefix from the biller
     * @param bool $sandbox sandbox mode
     * @param string $language language for the response payloads
     */
    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function initialize(Configurations $configurations) {
        if ($configurations->isSandbox()) {
            $this->baseURL = 'https://api-sandbox.partners.scb/partners/sandbox';
        } else {
            $this->baseURL = 'http://api.partners.scb/partners';
        }
        $this->merchant = trim($configurations->getMerchant());
        $this->terminal = trim($configurations->getTerminal());
        $this->language = mb_strtoupper(trim($configurations->getLanguage()));
        $this->biller = trim($configurations->getBiller());
        $this->appId = trim($configurations->getApplicationId());
        $this->prefix = mb_strtoupper(trim($configurations->getPrefix()));
        $this->authenticate($this->appId, trim($configurations->getApplicationSecret()));
    }

    public function is_initialized(): bool {
        return (bool) $this->token;
    }

    /**
     * Make a request on the API
     *
     * @param string $method method from the request
     * @param string $path path from the request
     * @param array $headers headers from the request
     * @param array $body body from the request
     *
     * @return result from the request
     */
    protected function request(string $method, string $path, $headers = [], array $body = [])
    {
        $request = $this->requestFactory->createRequest($method, $this->baseURL . $path);

        $request->withHeader('accept-language', $this->language);
        $request->withHeader('requestUId', $this->getNonce());
        $request->withHeader('Content-Type', 'application/json');

        foreach ($headers as $header => $value) {
            $request->withHeader($header, $value);
        }

        $body = json_encode($body);

        $request->withBody($this->streamFactory->createStream($body));

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new SCBPaymentAPIException('SCB API Request failed');
        }

        $data = json_decode($response->getBody()->getContents());
        if (! $data || !property_exists($data, 'status') || !property_exists($data->status, 'code') || $data->status->code != '1000' || !property_exists($data, 'data')) {
            throw new SCBPaymentAPIException('SCB API Request failed');
        }

        return $data->data;
    }

    /**
     * Create a nonce that is never the same
     *
     * @return string nonce
     */
    protected function getNonce(): string
    {
        return time() . uniqid();
    }

    /**
     * Authenficate  to the API
     *
     * @param string $appId ID from the application
     * @param string $appSecret Secret ID from the application
     */
    protected function authenticate(string $appId, string $appSecret): void
    {
        $headers = [
            'resourceOwnerId' => $appId,
        ];

        $body = [
            'applicationKey' => $appId,
            'applicationSecret' => $appSecret,
        ];

        try {
            $data = $this->request('POST', '/v1/oauth/token', $headers, $body);
        } catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to authenticate SCB API');
        }
        if (!property_exists($data, 'accessToken')) {
            throw new SCBPaymentAPIException('Fail to authenticate SCB API');
        }
        $this->token = $data->accessToken;
    }

    /**
     * Create a QRCode
     *
     * @param string $transactionID ID from the transaction
     * @param string $amount amount from the transaction
     *
     * @return stdClass QRCode created
     */
    public function createQRCode(string $transactionID, string $amount): stdClass
    {
        $this->failOnNotInitialize();

        $headers = [
            'authorization' => ' Bearer ' . $this->token,
            'resourceOwnerId' => $this->appId,
        ];

        $body = [
            'qrType' => 'PPCS',
            'ppType' => 'BILLERID',
            'ppId' => $this->biller,
            'amount' => (string) $amount,
            'ref1' => (string) $transactionID,
            'ref2' => (string) $transactionID,
            'ref3' => $this->prefix,
            'merchantId' => $this->merchant,
            'terminalId' => $this->terminal,
            'invoice' => (string) $transactionID,
            'csExtExpiryTime' => '60',
        ];
        try {
            $data = $this->request('POST', '/v1/payment/qrcode/create', $headers, $body);
        } catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to create SCB QRCode');
        }

        return $data;
    }

    /**
     * Check if a transaction is validated
     *
     * @param string $reference1 first reference from the transaction
     * @param string $reference2 second reference from the transaction
     * @param DateTime $transactionDate date from the transaction
     *
     * @return array transaction data
     */
    public function checkTransactionBillPayment(string $reference1, string $reference2, DateTime $transactionDate): array
    {
        $this->failOnNotInitialize();

        $headers = [
            'authorization' => 'Bearer ' . $this->token,
            'resourceOwnerId' => $this->appId,
        ];

        $transactionDate->setTimezone(new DateTimeZone('Asia/Bangkok'));

        $path = "/v1/payment/billpayment/inquiry?billerId={$this->biller}&reference1=${reference1}&reference2=${reference2}&transactionDate={$transactionDate->format('Y-m-d')}&eventCode=00300100";
        try {
            $data = $this->request('GET', $path, $headers);
        } catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }

        return $data;
    }

    /**
     * Check transaction status from the id transaction
     *
     * @param string $transationId id from the transaction
     *
     * @return array transaction data
     */
    public function checkTransactionCreditCardPayment(string $transationId)
    {
        $this->failOnNotInitialize();

        $headers = [
            'authorization' => 'Bearer ' . $this->token,
            'resourceOwnerId' => $this->appId,
        ];

        $path = "/v1/payment/qrcode/creditcard/${transationId}";

        try {
            $data = $this->request('GET', $path, $headers);
        } catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }

        return $data;
    }

    protected function failOnNotInitialize() {
        if(! $this->token) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }
    }
}
