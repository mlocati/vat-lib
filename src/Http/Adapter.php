<?php

namespace VATLib\Http;

interface Adapter
{
    /**
     * Check if the specific adapter is available.
     */
    public static function isAvailable();

    /**
     * @param string $url
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    public function getJson($url);

    /**
     * @param string $url
     * @param string $json
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return array{0: int, 1: string}
     */
    public function postJson($url, $json);
}
