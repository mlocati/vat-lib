<?php

namespace VATLib\Http\Adapter;

use VATLib\Exception\IOError;
use VATLib\Http\Adapter;

class Curl implements Adapter
{
    /**
     * @var array
     */
    private $defaultOptions;

    public function __construct(array $defaultOptions = [])
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::isAvailable()
     */
    public static function isAvailable()
    {
        return extension_loaded('curl');
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::getJson()
     */
    public function getJson($url)
    {
        return $this->invoke($url, []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        return $this->invoke($url, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
        ]);
    }

    /**
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    private function invoke($url, array $options)
    {
        $ch = $this->createCurl();
        try {
            $this->setCurlOptions($ch, [CURLOPT_URL => $url] + $options);
            $result = $this->runCurl($ch);
        } finally {
            curl_close($ch);
        }

        return $result;
    }

    /**
     * @throws \VATLib\Exception\IOError
     *
     * @return \CurlHandle|resource
     */
    private function createCurl()
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new IOError('curl_init() failed');
        }

        return $ch;
    }

    /**
     * @throws \VATLib\Exception\IOError
     *
     * @param \CurlHandle|resource $ch
     */
    private function setCurlOptions($ch, array $options)
    {
        $actualOptions = [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
        ];
        foreach ([$options, $this->defaultOptions] as $merge) {
            foreach ($merge as $key => $value) {
                if (!isset($actualOptions[$key])) {
                    $actualOptions[$key] = $value;
                    continue;
                }
                if (is_array($actualOptions[$key]) && is_array($value)) {
                    $actualOptions[$key] = array_merge($actualOptions[$key], $value);
                }
            }
        }
        if (!curl_setopt_array($ch, $actualOptions)) {
            throw $this->createCurlException('curl_setopt_array() failed', $ch);
        }
    }

    /**
     * @param \CurlHandle|resource $ch
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    private function runCurl($ch)
    {
        $response = curl_exec($ch);
        if (!is_string($response)) {
            throw $this->createCurlException('curl_exec() failed', $ch);
        }
        $statusCode = curl_getinfo($ch, defined('CURLINFO_RESPONSE_CODE') ? CURLINFO_RESPONSE_CODE : CURLINFO_HTTP_CODE);
        if (!is_int($statusCode)) {
            throw $this->createCurlException('curl_getinfo() failed', $ch);
        }
        return [$statusCode, $response];
    }

    /**
     * @param \CurlHandle|resource $ch

     * @return \VATLib\Exception\IOError
     */
    private function createCurlException($message, $ch)
    {
        $err = curl_error($ch);
        $err = is_string($err) ? trim($err) : '';
        if ($err !== '') {
            return new IOError("{$message}: {$err}");
        }
        $errno = curl_errno($ch);
        if (is_int($errno) && $errno !== 0) {
            return new IOError("{$message}: error code {$errno}");
        }

        return new IOError($message);
    }
}
