<?php
return [
    'NotConfiguredShouldRaiseAnError' => [
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
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/creditcard/transaction_id',
            'data' => [
                [
                    'test' => 'test'
                ]
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
    'ValidShouldReturnData' => [
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
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/creditcard/transaction_id',
            'data' => [
                [
                    'test' => 'test'
                ]
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
    'FailRequestShouldRaiseError' => [
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
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/qrcode/creditcard/transaction_id',
            'data' => [
                [
                    'test' => 'test'
                ]
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