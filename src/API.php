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

    public function __construct(string $appId, string $appSecret, string $merchant, string $terminal, string $biller, bool $sandbox = false, string $language = 'EN') {
        if($sandbox)
            $this->baseURL = 'https://api-sandbox.partners.scb/partners/sandbox';
        else
            $this->baseURL = 'http://api.partners.scb/partners';
        $this->merchant = $merchant;
        $this->terminal = $terminal;
        $this->language = $language;
        $this->biller = $biller;
        $this->appId = $appId;
        $this->authentificate($appId, $appSecret);
    }

    protected function request($method, $path, $headers=[], $body=[]): stdClass {
        $ch = curl_init();

        $headers[] = 'accept-language: ' . $this->language;
        $headers[] = 'requestUId: ' . $this->getNonce();

        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_STDERR, fopen('php://stderr', 'w'));

        try{
            if(mb_strtoupper($method) == 'POST'){
                $body = json_encode($body);
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            curl_setopt($ch, CURLOPT_URL, $this->baseURL . $path);
            curl_setopt($ch,     CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
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

    protected function authentificate(string $appId, string $appSecret) {
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

    public function createQRCode(string $transactionID, string $amount) {
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
            "ref3" => "XOU",
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
        $data;
    }
}