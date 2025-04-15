<?php

namespace VATLib\Http\Adapter;

use Exception;
use GuzzleHttp\Client;
use VATLib\Exception\IOError;
use VATLib\Http\Adapter;

class Guzzle implements Adapter
{
    /**
     * @var \GuzzleHttp\Client
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

    public function __construct(Client $client = null)
    {
        $this->client = $client === null ? new Client() : $client;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::getJson()
     */
    public function getJson($url)
    {
        return $this->invoke('GET', $url, []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        return $this->invoke('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $json,
        ]);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    private function invoke($method, $url, array $options)
    {
        $response = $this->performRequest($method, $url, $options);

        return $this->parseResponse($response);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function performRequest($method, $url, array $options)
    {
        try {
            return $this->client->request($method, $url, array_merge_recursive([
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
            ], $options));
        } catch (Exception $x) {
            throw new IOError($x->getMessage(), $x);
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array{0: int, 1: string}
     */
    private function parseResponse($response)
    {
        return [
            $response->getStatusCode(),
            (string) $response->getBody(),
        ];
    }
}
