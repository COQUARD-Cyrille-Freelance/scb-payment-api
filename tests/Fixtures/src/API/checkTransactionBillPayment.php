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
            'reference1' => 'reference1',
            'reference2' => 'reference2',
            'transaction_time' => new DateTime('01-01-1997'),
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    (object) [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/billpayment/inquiry?billerId=biller_id&reference1=reference1&reference2=reference2&transactionDate=1997-01-01&eventCode=00300100',
            'data' => [
                (object) [
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
            'reference1' => 'reference1',
            'reference2' => 'reference2',
            'transaction_time' => new DateTime('01-01-1997'),
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    (object) [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/billpayment/inquiry?billerId=biller_id&reference1=reference1&reference2=reference2&transactionDate=1997-01-01&eventCode=00300100',
            'data' => [
                (object) [
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
            'reference1' => 'reference1',
            'reference2' => 'reference2',
            'transaction_time' => new DateTime('01-01-1997'),
            'amount' => 100,
            'response_content' => json_encode([
                'status' => [
                    'code' => 1000,
                ],
                'data' => [
                    (object) [
                        'test' => 'test'
                    ]
                ]
            ]),
        ],
        'expected' => [
            'body' => json_encode([]),
            'method' => 'GET',
            'uri' => 'http://api.partners.scb/partners/v1/payment/billpayment/inquiry?billerId=biller_id&reference1=reference1&reference2=reference2&transactionDate=1997-01-01&eventCode=00300100',
            'data' => [
                (object) [
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