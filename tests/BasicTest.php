<?php


namespace SCBPaymentAPI\Tests;


use _HumbugBoxb11bbfeb1693\Nette\Utils\DateTime;
use SCBPaymentAPI\API;
use SCBPaymentAPI\Exceptions\SCBPaymentAPIException;

class BasicTest extends TestCase
{
    public function testFindTransaction() {
        $api = new API(
            'l7e8b9cda05251404ba73a1ac96032cdc1',
            'c5bba5b27ab24034a4cfb220e2eccd87',
            '766900478733720',
            '392041928593376',
            '702408872778985',
            'XOU',
            true,
            'TH'
        );

        try {
            $data = $api->checkTransactionCreditCardPayment('14');
        } catch (SCBPaymentAPIException $e) {
            $e->getMessage();
        }
    }

    public function testFindByReference() {
        $api = new API(
            'l7e8b9cda05251404ba73a1ac96032cdc1',
            'c5bba5b27ab24034a4cfb220e2eccd87',
            '766900478733720',
            '392041928593376',
            '702408872778985',
            'XOU',
            true,
            'TH'
        );

        try {
            $data = $api->checkTransactionBillPayment('14', '14', new DateTime('2020-11-17'));
        } catch (SCBPaymentAPIException $e) {
            $e->getMessage();
        }
    }

    public function testCreateQRCode() {
        $api = new API(
            'l7e8b9cda05251404ba73a1ac96032cdc1',
            'c5bba5b27ab24034a4cfb220e2eccd87',
            '766900478733720',
            '392041928593376',
            '702408872778985',
            'XOU',
            true
        );

        try {
            $data = $api->createQRCode('12', 12.1);
            var_dump($data);
            exit();
        } catch (SCBPaymentAPIException $e) {
            $e->getMessage();
        }
    }
}