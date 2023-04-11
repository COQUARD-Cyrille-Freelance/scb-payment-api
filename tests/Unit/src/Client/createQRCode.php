<?php

namespace SCBPaymentAPI\Tests\Unit\src\Client;

use CoquardCyrilleFreelance\SCBPaymentAPI\Client;
use CoquardCyrilleFreelance\SCBPaymentAPI\Configurations;
use CoquardCyrilleFreelance\SCBPaymentAPI\Exceptions\SCBPaymentAPIException;
use Mockery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SCBPaymentAPI\Tests\Fixtures\files\ClientException;
use SCBPaymentAPI\Tests\Unit\InitializeProperties;
use SCBPaymentAPI\Tests\Unit\TestCase;

class Test_CreateQRCode extends TestCase
{
    use InitializeProperties;

    protected $api;
    protected $client;
    protected $requestFactory;
    protected $streamFactory;
    protected $configurations;
    protected $request;

    protected $response;

    protected $response_stream;

    protected $stream;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(ClientInterface::class);
        $this->requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $this->streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $this->request = Mockery::mock(RequestInterface::class);
        $this->response = Mockery::mock(ResponseInterface::class);
        $this->response_stream = Mockery::mock(StreamInterface::class);
        $this->configurations = Mockery::mock(Configurations::class);
        $this->stream = Mockery::mock(StreamInterface::class);
        $this->api = new Client($this->client, $this->requestFactory, $this->streamFactory);
    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldReturnAsExpected($config, $expected) {
        $this->setProperties($this->api, $config['properties']);

        $this->configureSendRequest($config, $expected);
        $this->configureNotConfigured($config, $expected);

        $data = $this->api->createQRCode($config['transaction_id'], $config['amount']);

        $this->assertSuccess($config, $expected, $data);
    }

    protected function configureNotConfigured($config, $expected) {
        if($config['is_configured']) {
            return;
        }
        $this->expectException(SCBPaymentAPIException::class);
    }

    protected function configureSendRequest($config, $expected) {
        if(! $config['is_configured']) {
            return;
        }

        $this->requestFactory->expects()->createRequest($expected['method'], $expected['uri'])->andReturn($this->request);

        foreach ($expected['headers'] as $header => $value) {
            $this->request->expects()->withHeader($header, $value);
        }

        $this->streamFactory->expects()->createStream($expected['body'])->andReturn($this->stream);
        $this->request->expects()->withBody($this->stream);

        $this->configureResponseSuccess($config, $expected);
        $this->configureResponseFailure($config, $expected);
    }

    protected function configureResponseSuccess($config, $expected) {
        if($config['is_error']) {
            return;
        }
        $this->client->expects()->sendRequest($this->request)->andReturn($this->response);
        $this->response->expects()->getBody()->andReturn($this->response_stream);
        $this->response_stream->expects()->getContents()->andReturn($config['response_content']);
    }

    protected function assertSuccess($config, $expected, $data) {
        if($config['is_error']) {
            return;
        }
        $this->assertEquals($expected['data'], $data);
    }

    public function configureResponseFailure($config, $expected) {
        if(! $config['is_error']) {
            return;
        }

        $this->client->expects()->sendRequest($this->request)->andThrow(ClientException::class);
        $this->expectException(SCBPaymentAPIException::class);
    }
}