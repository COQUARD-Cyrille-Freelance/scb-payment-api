# SCB Payment API
This library is designed as a PHP SDK for [SCB Payment Gateway](https://www.scb.co.th/en/corporate-banking/business-cash-management/scb-business-collection/scb-payment-gateway.html).
## Install 
To install that library run the following command:
`composer require coquardcyr/scb-payment-api`
## Usage
Once you installed the library, you can interact with the client class.

First you will have to use libraries that implements:
- [psr-17](https://www.php-fig.org/psr/psr-17/): HTTP factories. 
- [psr-7](https://www.php-fig.org/psr/psr-7/): HTTP Client.

Then you will have to instance the `CoquardCyrilleFreelance\SCBPaymentAPI\Client` class with both libraries you chose.

Once this is done you will then have to initialize the connexion with the method `initialize` that takes in parameters a class implementing the `CoquardCyrilleFreelance\SCBPaymentAPI\Configurations` interface.
This will initialize the connexion to the API by creating a bearer token.

The next step is to create the QR code for that you can use the `createQRCode` with the transaction ID with the amount.
This method will return the QR code data:
```php
[
    "qrRawData" => "00020101021230670016A00000067701011201151234567890123450210REFERENCE10310REFERENCE252047011530376454041.005802TH6007BANGKOK62070703SCB6304CE18",
    "qrImage" => "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"
]
```

Once you're done that then you can use both `checkTransactionBillPayment` and `checkTransactionCreditCardPayment` to check the status from the transaction:
- `checkTransactionBillPayment` takes reference1 and reference2 from the transaction and the `Datetime` from the transaction and return the list of transactions matching:
```php
[
    "transRef"=> "201904227kAylzNUdnJ1z0w",
    "sendingBank"=> "014",
    "receivingBank"=> "014",
    "transDate"=> "20190422",
    "transTime" => "14:04:35",
    "sender"=> [
      "displayName"=> "Sandbox customer",
      "name"=> "Sandbox customer",
      "proxy"=> [
        "type"=> "MSISDN",
        "value"=> "0812345678"
      ],
      "account"=> [
        "type"=> "BANKAC",
        "value"=> "1234567890"
      ]
    ],
    "receiver"=> [
      "displayName"=> "Sandbox Shop",
      "name"=> "Sandbox Shop",
      "proxy"=> [
        "type"=> "BILLERID",
        "value"=> "311040039475171"
      ],
      "account"=> [
        "type"=> "BANKAC",
        "value"=> "0987654321"
      ]
    ],
    "amount"=> "500",
    "paidLocalAmount"=> "500",
    "paidLocalCurrency"=> "764",
    "countryCode"=> "TH",
    "ref1"=> "12345678",
    "ref2"=> "Test",
    "ref3"=> "1234"
]
```
- `checkTransactionCreditCardPayment` takes the transaction ID as parameter and return the transaction:
```php
[
    "transactionId" => "20190910121630805000000",
    "amount" => "8284.50",    
    "transactionDateandTime" => "20190912 10:32:46",
    "merchantPAN" => "4761320000000011", 
    "consumerPAN" => "999999xxxxxx0001",
    "currencyCode" => "764",
    "merchantId" => "447434058100730",
    "terminalId" => "073334824590333",
    "qrId" => "20190910121630805000000",   
    "traceNo" => "000001", 
    "authorizeCode" >=> "489332",
    "paymentMethod" => "QRCS",
    "transactionType" => "SETTLED",
    "channelCode" => "VISA",
    "invoice" => "SCB0001",
    "note" => "Additional note"
]
```