<?php


namespace SCBPaymentAPI;


use Exception;
use SCBPaymentAPI\Exceptions\SCBPaymentAPIException;
use stdClass;

class API
{
    protected $baseURL = '';
    protected $token = '';
    protected $language = '';
    protected $appId = '';
    protected $merchant = '';
    protected $terminal = '';
    protected $biller = '';
    protected $prefix = '';

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

    protected function request($method, $path, $headers=[], $body=[]): stdClass {
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

    protected function getNonce(): string {
        return time() . uniqid();
    }

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

    public function checkTransactionBillPayment(string $reference1, string $reference2, \DateTime $transactionDate): stdClass {
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

    public function checkTransactionCreditCardPayment(string $QRCodeId): stdClass {
        $headers = [
            'authorization: Bearer ' . $this->token,
            'resourceOwnerId: ' . $this->appId,
        ];

        $path = "/v1/payment/qrcode/creditcard/${QRCodeId}";

        try {
            $data = $this->request('GET', $path, $headers);
        }catch (SCBPaymentAPIException $e) {
            throw new SCBPaymentAPIException('Fail to get the transaction');
        }
        return $data;
    }
}