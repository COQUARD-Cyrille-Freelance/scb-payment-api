<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */


namespace SCBPaymentAPI\Tests;

use _HumbugBoxb11bbfeb1693\Nette\Utils\DateTime;
use SCBPaymentAPI\API;
use SCBPaymentAPI\Exceptions\SCBPaymentAPIException;

class BasicTest extends TestCase
{
    public function testFindTransaction()
    {
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
            $data = $api->checkTransactionCreditCardPayment('20201122041802819000000');
        } catch (SCBPaymentAPIException $e) {
            $e->getMessage();
        }
    }

    public function testFindByReference()
    {
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

    public function testCreateQRCode()
    {
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
