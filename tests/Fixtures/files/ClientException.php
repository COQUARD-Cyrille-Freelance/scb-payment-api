<?php

namespace SCBPaymentAPI\Tests\Fixtures\files;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends Exception implements ClientExceptionInterface
{

}