<?php


namespace SCBPaymentAPI;


use Exception;
use SCBPaymentAPI\Exceptions\SCBPaymentAPIException;
use stdClass;

/**
 * Class API that wrap SCB API to prevent strong dependencies on it
 * @package SCBPaymentAPI
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
     * API constructor.
     * @param string $appId ID from the application
     * @param string $appSecret Secret ID from the application
     * @param string $merchant ID from the merchant
     * @param string $terminal ID from the terminal
     * @param string $biller ID from the biller
     * @param string $prefix Reference prefix from the biller
     * @param bool $sandbox sandbox mode
     * @param string $language language for the response payloads
     */
    public function __construct(string $appId, string $appSecret, string $merchant, string $terminal, string $biller, string $prefix, bool $sandbox = false, string $language = 'EN') {
        if($sandbox)
            $this->baseURL = 'https://api-sandbox.partners.scb/partners/sandbox';
        else
            $this->baseURL = 'http://api.partners.scb/partners';
        $this->merchant = trim($merchant);
        $this->terminal = trim($terminal);
        $this->language = mb_strtoupper(trim($language));
        $this->biller = trim($biller);
        $this->appId = trim($appId);
        $this->authentificate(trim($appId), trim($appSecret));
        $this->prefix = mb_strtoupper(trim($prefix));
    }

    /**
     * Make a request on the API
     * @param string $method method from the request
     * @param string $path path from the request
     * @param array $headers headers from the request
     * @param array $body body from the request
     * @return result from the request
     */
    protected function request(string $method, string $path, $headers=[], array $body=[]) {
        $ch = curl_init();

        $headers[] = 'accept-language: ' . $this->language;
        $headers[] = 'requestUId: ' . $this->getNonce();

        try{

            $headers[] = 'Content-Type: application/json';

            $body = json_encode($body);

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseURL . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => mb_strtoupper($method),
                CURLOPT_HTTPHEADER => $headers,
            ]);
            if(mb_strtoupper($method) == 'POST')
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $result = curl_exec($ch);
            $data = json_decode($result);
            if(! property_exists($data, 'status') || ! property_exists($data->status, 'code') || $data->status->code != '1000' || ! property_exists($data, 'data'))
                throw new SCBPaymentAPIException('SCB API Request failed');
            curl_close($ch);
            return $data->data;
        }catch (Exception $e) {
            curl_close($ch);
            throw new SCBPaymentAPIException();
        }
    }

    /**
     * Create a nonce that is never the same
     * @return string nonce
     */
    protected function getNonce(): string {
        return time() . uniqid();
    }

    /**
     * Authenficate  to the API
     * @param string $appId ID from the application
     * @param string $appSecret Secret ID from the application
     */
    protected function authentificate(string $appId, string $appSecret): void {
        $headers = [
            'resourceOwnerId: ' . $appId,
        ];

        $body = [
            "applicationKey" => $appId,
            "applicationSecret" => $appSecret,
        ];

        try {
            $data = $this->request('POST', '/v1/oauth/token', $headers, $body);
        }catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to authenticate SCB API');
        }
        if(! property_exists($data, 'accessToken'))
            throw new SCBPaymentAPIException('Fail to authenticate SCB API');
        $this->token = $data->accessToken;
    }

    /**
     * Create a QRCode
     * @param string $transactionID ID from the transaction
     * @param string $amount amount from the transaction
     * @return stdClass QRCode created
     */
    public function createQRCode(string $transactionID, string $amount): stdClass {
        $headers = [
            'authorization: Bearer ' . $this->token,
            'resourceOwnerId: ' . $this->appId,
        ];

        $body = [
            "qrType"=> "PPCS",
            "ppType"=> "BILLERID",
            "ppId"=> $this->biller,
            "amount"=> (string) $amount,
            "ref1" => (string) $transactionID,
            "ref2" => (string) $transactionID,
            "ref3" => $this->prefix,
            "merchantId"=> $this->merchant,
            "terminalId"=> $this->terminal,
            "invoice"=> (string) $transactionID,
            "csExtExpiryTime" => "60"
        ];
        try{
          $data = $this->request('POST', '/v1/payment/qrcode/create', $headers, $body);
        }catch (SCBPaymentAPIException $e){
            throw new SCBPaymentAPIException('Fail to create SCB QRCode');
        }
        return $data;
    }

    /**
     * Check if a transaction is validated
     * @param string $reference1 first reference from the transaction
     * @param string $reference2 second reference from the transaction
     * @param \DateTime $transactionDate date from the transaction
     * @return array transaction data
     */
    public function checkTransactionBillPayment(string $reference1, string $reference2, \DateTime $transactionDate): array {
        $headers = [
            'authorization: Bearer ' . $this->token,
            'resourceOwnerId: ' . $this->appId,
        ];
        $path = "/v1/payment/billpayment/inquiry?billerId={$this->biller}&reference1=${reference1}&reference2=${reference2}&transactionDate={$transactionDate->format('Y-m-d')}&eventCode=00300100";
        try {
            $data = $this->request('GET', $path, $headers);
        }catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }
        return $data;
    }

    /**
     * Check transaction status from the id transaction
     * @param string $transationId id from the transaction
     * @return array transaction data
     */
    public function checkTransactionCreditCardPayment(string $transationId): array {
        $headers = [
            'authorization: Bearer ' . $this->token,
            'resourceOwnerId: ' . $this->appId,
        ];

        $path = "/v1/payment/qrcode/creditcard/${$transationId}";

        try {
            $data = $this->request('GET', $path, $headers);
        }catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }
        return $data;
    }
}