<?php

namespace VATLib\Test\Service;

use VATLib\Vies\CheckVat;
use VATLib\Vies\Client;

class ViesClientWrapper extends Client
{
    /**
     * @var int
     */
    public $checkStatusCalls = 0;

    /**
     * @var int
     */
    public $checkVatNumberCalls = 0;

    /**
     * @var bool
     */
    private $useFakeServer = true;

    /**
     * @return \VATLib\Http\Adapter
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * @param bool $useFakeServer
     */
    public function setUseFakeServer($value)
    {
        $this->useFakeServer = (bool) $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Vies\Client::checkStatus()
     */
    public function checkStatus()
    {
        $this->checkStatusCalls++;

        return parent::checkStatus();
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Vies\Client::checkVatNumber()
     */
    public function checkVatNumber(CheckVat\Request $request)
    {
        $this->checkVatNumberCalls++;

        return parent::checkVatNumber($request);
    }
    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Vies\Client::getBaseUrl()
     */
    protected function getBaseUrl()
    {
        return $this->useFakeServer ? FakeViesServerManager::getRootURL() : parent::getBaseUrl();
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Vies\Client::getCheckStatusPath()
     */
    protected function getCheckStatusPath()
    {
        return parent::getCheckStatusPath() . ($this->useFakeServer ? '.php' : '');
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Vies\Client::getCheckVatNumberPath()
     */
    protected function getCheckVatNumberPath()
    {
        return parent::getCheckVatNumberPath() . ($this->useFakeServer ? '.php' : '');
    }
}
