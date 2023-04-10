<?php

return [
  'InitializationSucceedShouldInitialize' => [
      'config' => [
          'is_sandbox' => false,
          'language' => 'EN',
          'prefix' => 'prefix',
          'biller' => 'biller',
          'terminal' => 'terminal',
          'merchant' => 'merchant',
          'application_id' => 'application_id',
          'application_secret' => 'application_secret',
          'is_error' => false,
          'response_content' => '{"status":{"code":1000},"data" : {"accessToken": "accessToken"}}'
      ],
      'expected' => [
          'method' => 'POST',
          'uri' => 'http://api.partners.scb/partners/v1/oauth/token',
          'body' => '{"applicationKey":"application_id","applicationSecret":"application_secret"}',
          'headers' => [
              'requestUId' => Mockery::any(),
              'accept-language' => 'EN',
              'Content-Type' => 'application/json',
              'resourceOwnerId' => 'application_id',
          ]
      ]
  ],
    'InitializationSandboxShouldInitializeWithSandboxURL' => [
        'config' => [
            'is_sandbox' => true,
            'language' => 'EN',
            'prefix' => 'prefix',
            'biller' => 'biller',
            'terminal' => 'terminal',
            'merchant' => 'merchant',
            'application_id' => 'application_id',
            'application_secret' => 'application_secret',
            'is_error' => false,
            'response_content' => '{"status":{"code":1000},"data" : {"accessToken": "accessToken"}}'
        ],
        'expected' => [
            'method' => 'POST',
            'uri' => 'https://api-sandbox.partners.scb/partners/sandbox/v1/oauth/token',
            'body' => '{"applicationKey":"application_id","applicationSecret":"application_secret"}',
            'headers' => [
                'requestUId' => Mockery::any(),
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'resourceOwnerId' => 'application_id',
            ]
        ]
    ],
    'InitializationFailShouldRaiseException' => [
        'config' => [
            'is_sandbox' => false,
            'language' => 'EN',
            'prefix' => 'prefix',
            'biller' => 'biller',
            'terminal' => 'terminal',
            'merchant' => 'merchant',
            'application_id' => 'application_id',
            'application_secret' => 'application_secret',
            'is_error' => true,
            'response_content' => ''
        ],
        'expected' => [
            'method' => 'POST',
            'uri' => 'http://api.partners.scb/partners/v1/oauth/token',
            'body' => '{"applicationKey":"application_id","applicationSecret":"application_secret"}',
            'headers' => [
                'requestUId' => Mockery::any(),
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'resourceOwnerId' => 'application_id',
            ]
        ]
    ]
];