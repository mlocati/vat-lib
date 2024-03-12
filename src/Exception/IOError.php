<?php

namespace VATLib\Exception;

use VATLib\Exception as BaseException;

class IOError extends BaseException
{
    /**
     * @param string $message
     * @param \Exception|\Throwable $previous
     */
    public function __construct($message, $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
