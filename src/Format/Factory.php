<?php

namespace VATLib\Format;

use ReflectionClass;
use RuntimeException;
use VATLib\Format;

class Factory
{
    /**
     * @var \VATLib\Format[]
     */
    private $formats = [];

    public function __construct()
    {
        $this->registerDirectory(Format::class, __DIR__);
    }

    /**
     * @param string|mixed $namespace
     * @param string|mixed $path
     * @param bool $ignoreDuplicates
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function registerDirectory($namespace, $path, $ignoreDuplicates = false)
    {
        if (!is_string($namespace)) {
            throw new RuntimeException('Please specify the namespace of the syntax checkers');
        }
        $namespace = trim($namespace, '\\');
        if ($namespace !== '') {
            $namespace .= '\\';
        }
        if (!is_string($path) || $path === '') {
            throw new RuntimeException('Please specify the directory containing the syntax checkers');
        }
        $realPath = realpath($path);
        if ($realPath === false || !is_dir($realPath)) {
            throw new RuntimeException("Failed to find the directory {$path}");
        }
        $contents = is_readable($realPath) ? scandir($realPath) : false;
        if ($contents === false) {
            throw new RuntimeException("Failed to list the content of the directory {$path}");
        }
        foreach ($contents as $file) {
            if (preg_match('/^\w\S*\.php/', $file)) {
                $this->registerClass($namespace . basename($file, '.php'), true, $ignoreDuplicates);
            }
        }

        return $this;
    }

    /**
     * @param string $className
     * @param bool $ignoreErrors
     * @param bool $ignoreDuplicates
     *
     * @throws \RuntimeException if $ignoreErrors is false
     */
    public function registerClass($className, $ignoreErrors = false, $ignoreDuplicates = false)
    {
        if (!is_string($className) || $className === '') {
            if (!$ignoreErrors) {
                throw new RuntimeException('Class name not specified');
            }
        } elseif (!class_exists($className)) {
            if (!$ignoreErrors) {
                throw new RuntimeException("Class not found: {$className}");
            }
        } elseif (!is_a($className, Format::class, true)) {
            if (!$ignoreErrors) {
                throw new RuntimeException("Class {$className} is not an instance of " . Format::class);
            }
        } else {
            $classInfo = new ReflectionClass($className);
            if ($classInfo->isAbstract()) {
                if (!$ignoreErrors) {
                    throw new RuntimeException("Class {$className} is an abstract class");
                }
            } else {
                $this->register(new $className(), $ignoreDuplicates);
            }
        }

        return $this;
    }

    /**
     * @param bool $ignoreDuplicates
     *
     * @return $this
     */
    public function register(Format $format, $ignoreDuplicates = false)
    {
        if (!$ignoreDuplicates) {
            $class = get_class($format);
            foreach ($this->formats as $existing) {
                if (get_class($existing) === $class) {
                    return $this;
                }
            }
        }

        $this->formats[] = $format;

        return $this;
    }

    /**
     * @return \VATLib\Format[]
     */
    public function getAllFormats()
    {
        return $this->formats;
    }

    /**
     * @param string|mixed $countryCode
     *
     * @return \VATLib\Format[]
     */
    public function getFormatsForCountry($countryCode)
    {
        $countryCode = is_string($countryCode) ? strtoupper($countryCode) : '';
        if ($countryCode === '') {
            return [];
        }
        return array_values(array_filter($this->formats, static function (Format $format) use ($countryCode) {
            return $format->getCountryCode() === $countryCode;
        }));
    }

    /**
     * @param string|mixed $vatNumber
     *
     * @return \VATLib\Format[]
     */
    public function getFormatsByPrefix($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? strtoupper($vatNumber) : '';
        if ($vatNumber === '') {
            return [];
        }
        return array_values(array_filter($this->formats, static function (Format $format) use ($vatNumber) {
            $prefix = $format->getVatNumberPrefix();
            return $prefix !== '' && strpos($vatNumber, $prefix) === 0;
        }));
    }
}
