<?php
return [
    'createQRCodeWithoutInitializationShouldRaiseError' => [
        'config' => [
            'properties' => [
                'token' => '',
                'baseURL' => 'http://api.partners.scb/partners',
                'language' => 'EN',
                'appId' => 'application_id',
                'merchant' => 'merchant_id',
                'terminal' => 'terminal_id',
                'biller' => 'biller_id',
                'prefix' => 'prefix',
            ],
            'is_error' => false,
            'is_configured' => false,
            'transaction_id' => 'transaction_id',
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => (object) [
                    'test' => 'test'
                ]
            ]),
        ],
        'expected' => [
            'body' => '{"qrType":"PPCS","ppType":"BILLERID","ppId":"biller_id","amount":"100","ref1":"transaction_id","ref2":"transaction_id","ref3":"prefix","merchantId":"merchant_id","terminalId":"terminal_id","invoice":"transaction_id","csExtExpiryTime":"60"}',
            'method' => 'POST',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/create',
            'data' => (object) [
                'test' => 'test'
            ],
            'headers' => [
                'requestUId' => Mockery::any(),
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer token',
                'resourceOwnerId' => 'application_id',
            ]
        ]
    ],
    'createQRCodeShouldReturnData' => [
        'config' => [
            'properties' => [
                'token' => 'token',
                'baseURL' => 'http://api.partners.scb/partners',
                'language' => 'EN',
                'appId' => 'application_id',
                'merchant' => 'merchant_id',
                'terminal' => 'terminal_id',
                'biller' => 'biller_id',
                'prefix' => 'prefix',
            ],
            'is_error' => false,
            'is_configured' => true,
            'transaction_id' => 'transaction_id',
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    'test' => 'test'
                ]
            ]),
        ],
        'expected' => [
            'body' => '{"qrType":"PPCS","ppType":"BILLERID","ppId":"biller_id","amount":"100","ref1":"transaction_id","ref2":"transaction_id","ref3":"prefix","merchantId":"merchant_id","terminalId":"terminal_id","invoice":"transaction_id","csExtExpiryTime":"60"}',
            'method' => 'POST',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/create',
            'data' => (object) [
                'test' => 'test'
            ],
            'headers' => [
                'requestUId' => Mockery::any(),
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer token',
                'resourceOwnerId' => 'application_id',
            ]
        ]
    ],
    'createQRCodeWithErrorShouldRaiseError' => [
        'config' => [
            'properties' => [
                'token' => 'token',
                'baseURL' => 'http://api.partners.scb/partners',
                'language' => 'EN',
                'appId' => 'application_id',
                'merchant' => 'merchant_id',
                'terminal' => 'terminal_id',
                'biller' => 'biller_id',
                'prefix' => 'prefix',
            ],
            'is_error' => true,
            'is_configured' => true,
            'transaction_id' => 'transaction_id',
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    'test' => 'test'
                ]
            ]),
        ],
        'expected' => [
            'body' => '{"qrType":"PPCS","ppType":"BILLERID","ppId":"biller_id","amount":"100","ref1":"transaction_id","ref2":"transaction_id","ref3":"prefix","merchantId":"merchant_id","terminalId":"terminal_id","invoice":"transaction_id","csExtExpiryTime":"60"}',
            'method' => 'POST',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/create',
            'data' => (object) [
                'test' => 'test'
            ],
            'headers' => [
                'requestUId' => Mockery::any(),
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer token',
                'resourceOwnerId' => 'application_id',
            ]
        ]
    ]
];