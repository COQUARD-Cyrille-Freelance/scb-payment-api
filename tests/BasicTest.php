<?php


namespace SCBPaymentAPI\Tests;


use SCBPaymentAPI\API;

class BasicTest extends TestCase
{
    public function testBasic(){
        $api = new API('l7e8b9cda05251404ba73a1ac96032cdc1', 'c5bba5b27ab24034a4cfb220e2eccd87','766900478733720',' 392041928593376', '702408872778985', true, 'EN');
        $api->createQRCode('12345', '20');
    }
}