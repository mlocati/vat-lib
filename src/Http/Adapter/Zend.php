<?php

namespace VATLib\Http\Adapter;

use Exception;
use VATLib\Exception\IOError;
use VATLib\Http\Adapter;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;

class Zend implements Adapter
{
    /**
     * @var \Zend\Http\Client
     */
    private $client;

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::isAvailable()
     */
    public static function isAvailable()
    {
        return class_exists(Client::class);
    }

    /**
     * @param \Zend\Http\Client|null $client
     */
    public function __construct($client = null)
    {
        $this->client = $client instanceof Client ? $client : new Client();
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::getJson()
     */
    public function getJson($url)
    {
        $request = new Request();
        $request
            ->setUri($url)
            ->setMethod('GET')
        ;

        return $this->invoke($request);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        $request = new Request();
        $request
            ->setUri($url)
            ->setMethod('POST')
            ->setContent($json)
        ;
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        return $this->invoke($request);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    private function invoke(Request $request)
    {
        if (!$request->getHeaders()->has('Accept')) {
            $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        }
        try {
            $response = $this->performRequest($request);
        } catch (Exception $x) {
            throw new IOError($x->getMessage(), $x);
        }

        return $this->parseResponse($response);
    }

    /**
     * @return \Zend\Http\Response
     */
    private function performRequest(Request $request)
    {
        return $this->client->send($request);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function parseResponse(Response $response)
    {
        return [
            $response->getStatusCode(),
            $response->getBody(),
        ];
    }
}
