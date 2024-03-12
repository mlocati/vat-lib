<?php

namespace VATLib\Exception;

use VATLib\Exception as BaseException;

class MissingPHPExtensions extends BaseException
{
    /**
     * @var string[]
     */
    private $phpExtensions;

    /**
     * @param string[] $phpExtensions
     * @param string $message
     */
    public function __construct(array $phpExtensions, $message = '')
    {
        parent::__construct($message ?: ("Missing PHP extensions: " . implode(', ', $phpExtensions)));
        $this->phpExtensions = $phpExtensions;
    }

    /**
     * @return string[]
     */
    public function getPHPExtensions()
    {
        return $this->phpExtensions;
    }
}
