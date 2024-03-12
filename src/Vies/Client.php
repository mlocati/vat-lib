<?php

namespace VATLib\Vies;

use VATLib\Exception\IOError;
use VATLib\Http\Adapter;

class Client
{
    const DEFAULT_BASE_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/';

    /**
     * @var \VATLib\Http\Adapter
     * @internal
     */
    protected $httpAdapter;

    public function __construct(Adapter $httpAdapter = null)
    {
        $this->httpAdapter = $httpAdapter ?: $this->buildHttpAdapter();
    }

    /**
     * Check the status of each member states.
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return \VATLib\Vies\CheckStatus\Response
     */
    public function checkStatus()
    {
        list($statusCode, $responseBody) = $this->httpAdapter->getJson($this->getBaseUrl() . $this->getCheckStatusPath());
        if ($statusCode !== 200) {
            throw $this->buildResponseException($statusCode, $responseBody);
        }

        return new CheckStatus\Response($this->decodeJson($responseBody));
    }

    /**
     * @throws \VATLib\Exception\IOError
     *
     * @return \VATLib\Vies\CheckVat\Response
     */
    public function checkVatNumber(CheckVat\Request $request)
    {
        // @see https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl
        if (!preg_match('/^[A-Z]{2}$/D', $request->getCountryCode())) {
            throw new IOError\Vies(IOError\Vies::VIESCODE_INVALID_INPUT, 'Missing or invalid field: country code');
        }
        // @see https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl
        if (!preg_match('/^[0-9A-Za-z+*.]{2,12}$/D', $request->getVatNumber())) {
            throw new IOError\Vies(IOError\Vies::VIESCODE_INVALID_INPUT, 'Missing or invalid field: VAT number');
        }
        list($statusCode, $responseBody) = $this->httpAdapter->postJson(
            $this->getBaseUrl() . $this->getCheckVatNumberPath(),
            json_encode($request)
        );
        if ($statusCode !== 200) {
            throw $this->buildResponseException($statusCode, $responseBody);
        }
        $data = $this->decodeJson($responseBody);
        if (isset($data['actionSucceed']) && $data['actionSucceed'] === false) {
            $error = $this->unserializeFailureResponse($data);
            throw ($error ?: new IOError('Operation failed'));
        }

        return new CheckVat\Response($data);
    }

    /**
     * @throws \VATLib\Exception\IOError
     *
     * @return \VATLib\Http\Adapter
     */
    private function buildHttpAdapter()
    {
        if (Adapter\Guzzle::isAvailable()) {
            return new Adapter\Guzzle();
        }
        if (Adapter\Curl::isAvailable()) {
            return new Adapter\Curl();
        }
        if (Adapter\Zend::isAvailable()) {
            return new Adapter\Zend();
        }
        if (Adapter\Stream::isAvailable()) {
            return new Adapter\Stream();
        }

        throw new IOError('No HTTP adapter is available. You can add Guzzle to your project, or enable the HTTP stream wrapper of PHP');
    }

    /**
     * @return string
     * @internal
     */
    protected function getCheckStatusPath()
    {
        return 'check-status';
    }

    /**
     * @return string
     * @internal
     */
    protected function getCheckVatNumberPath()
    {
        return 'check-vat-number';
    }

    /**
     * @param string $json
     *
     * @throws \VATLib\Exception\IOError
     *
     * @return mixed
     */
    private function decodeJson($json)
    {
        if ($json === 'null') {
            return null;
        }
        $result = json_decode($json, true);
        if ($result  === null) {
            throw new IOError("Failed to decode the following JSON:\n{$json}");
        }

        return $result;
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \VATLib\Exception\IOError
     */
    private function buildResponseException($statusCode, $responseBody)
    {
        return $this->buildResponseExceptionFromResponseBody($statusCode, $responseBody) ?: $this->buildResponseExceptionFromStatusCode($statusCode, $responseBody);
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \VATLib\Exception\IOError|null
     */
    private function buildResponseExceptionFromResponseBody($statusCode, $responseBody)
    {
        if (!$responseBody) {
            return null;
        }
        try {
            $data = $this->decodeJson($responseBody);
        } catch (IOError $_) {
            return null;
        }
        if (!is_array($data)) {
            return null;
        }

        return $this->unserializeFailureResponse($data);
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \VATLib\Exception\IOError
     */
    private function buildResponseExceptionFromStatusCode($statusCode, $responseBody)
    {
        switch ($statusCode) {
            case 400:
                return new IOError('Bad Request');
            case 412:
                return new IOError($responseBody);
            case 500:
                return new IOError('Internal server error');
        }

        return new IOError("Unexpected HTTP response code: {$statusCode}");
    }

    /**
     * @return string
     * @internal
     */
    protected function getBaseUrl()
    {
        return static::DEFAULT_BASE_URL;
    }

    /**
     * @return \VATLib\Exception\IOError\Vies|null
     */
    private function unserializeFailureResponse(array $response)
    {
        if (!isset($response['errorWrappers']) || !is_array($response['errorWrappers'])) {
            return null;
        }
        $withCode = null;
        $otherErrors = [];
        foreach ($response['errorWrappers'] as $errorWrapper) {
            if (!is_array($errorWrapper)) {
                continue;
            }
            $errorCode = isset($errorWrapper['error']) && is_string($errorWrapper['error']) ? trim($errorWrapper['error']) : '';
            $errorMessage = isset($errorWrapper['message']) && is_string($errorWrapper['message']) ? trim($errorWrapper['message']) : '';
            if ($errorCode === '' && $errorMessage === '') {
                continue;
            }
            $error = new IOError\Vies($errorCode, $errorMessage);
            if ($withCode === null && $errorCode !== null) {
                $withCode = $error;
            } else {
                $otherErrors[] = $error;
            }
        }
        if ($withCode === null) {
            $withCode = array_shift($otherErrors);
            if ($withCode === null) {
                return null;
            }
        }

        return $withCode->setOtherErrors($otherErrors);
    }
}
