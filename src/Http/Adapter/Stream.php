<?php

namespace VATLib\Http\Adapter;

use VATLib\Exception\IOError;
use VATLib\Http\Adapter;

class Stream implements Adapter
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
        return in_array('http', stream_get_wrappers(), true);
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
            'header' => [
                'Content-Type: application/json',
            ],
            'method' => 'POST',
            'content' => $json,
        ]);
    }

    /**
    /**
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    private function invoke($url, array $options)
    {
        $context = $this->createContext($options);
        $http_response_header = [];
        $whyNot = '';
        set_error_handler(
            static function ($errno, $errstr) use (&$whyNot) {
                if ($whyNot === '' && is_string($errstr)) {
                    $whyNot = trim($errstr);
                }
            },
            -1
        );
        try {
            $response = file_get_contents($url, false, $context);
        } finally {
            restore_error_handler();
        }
        if ($response === false) {
            throw new IOError($whyNot ?: 'file_get_contents() failed');
        }

        return $this->parseResponse($response, $http_response_header);
    }

    /**
     * @return resource
     */
    private function createContext(array $options)
    {
        $actualOptions = [
            'header' => [
                'Accept: application/json',
            ],
            'ignore_errors' => true,
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

        return stream_context_create(['http' => $actualOptions]);
    }

    /**
     * @param string $response
     * @param string[] $httpResponseHeaders
     *
     * @return array{0: int, 1: string}
     */
    private function parseResponse($response, array $httpResponseHeaders)
    {
        $chunks = $httpResponseHeaders === [] ? [] : explode(' ', $httpResponseHeaders[0], 3);

        return [
            isset($chunks[1]) && is_numeric($chunks[1]) ? (int) $chunks[1] : 0,
            $response,
        ];
    }
}
