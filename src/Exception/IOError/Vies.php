<?php

namespace VATLib\Exception\IOError;

use VATLib\Exception\IOError;

class Vies extends IOError
{
    /**
     * The provided country code is invalid or the VAT number is empty.
     *
     * @var string
     */
    const VIESCODE_INVALID_INPUT = 'INVALID_INPUT';

    /**
     * The request cannot be processed due to high traffic on the web application.
     * Retry later.
     *
     * @var string
     */
    const VIESCODE_GLOBAL_MAX_CONCURRENT_REQ = 'GLOBAL_MAX_CONCURRENT_REQ';

    /**
     * The request cannot be processed due to high traffic towards the Member State you are trying to reach.
     * Retry later.
     *
     * @var string
     */
    const VIESCODE_MS_MAX_CONCURRENT_REQ = 'MS_MAX_CONCURRENT_REQ';

    /**
     * An error was encountered either at the network level or the web application level.
     * Retry later.
     *
     * @var string
     */
    const VIESCODE_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    /**
     * The application at the member state is not replying or not available.
     * Retry later.
     *
     * @var string
     */
    const VIESCODE_MS_UNAVAILABLE = 'MS_UNAVAILABLE';

    /**
     * The application did not receive a reply within the allocated time period.
     * Retry later.
     *
     * @var string
     */
    const VIESCODE_TIMEOUT = 'TIMEOUT';

    /**
     * @var string
     */
    private $viesCode;

    /**
     * @var string
     */
    private $viesMessage;

    /**
     * @var \VATLib\Exception\IOError\Vies[]
     */
    private $otherErrors = [];

    /**
     * @param string $viesCode
     * @param string $viesMessage
     */
    public function __construct($viesCode, $viesMessage)
    {
        $viesCode = (string) $viesCode;
        $viesMessage = (string) $viesMessage;
        parent::__construct(trim("[{$viesCode}] {$viesMessage}"));
        $this->viesCode = (string) $viesCode;
        $this->viesMessage = (string) $viesMessage;
    }

    /**
     * @return string
     */
    public function getViesCode()
    {
        return $this->viesCode;
    }

    /**
     * @return string
     */
    public function getViesMessage()
    {
        return $this->viesMessage;
    }

    /**
     * @param \VATLib\Exception\IOError\Vies[] $value
     *
     * @return $this
     */
    public function setOtherErrors(array $value)
    {
        $this->otherErrors = $value;

        return $this;
    }

    /**
     * @return \VATLib\Exception\IOError\Vies[]
     */
    public function getOtherErrors()
    {
        return $this->otherErrors;
    }
}
