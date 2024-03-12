<?php

namespace VATLib\Test\Service;

use VATLib\Vies\Client;

class ViesClientWrapper extends Client
{
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
