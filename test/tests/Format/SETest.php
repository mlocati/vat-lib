<?php

namespace VATLib\Test\Format;

use PHPUnit\Framework\TestCase;
use VATLib\Format\SE;

class SETest extends TestCase
{
    public function testFormatShort()
    {
        $formatter = new SE();
        $formatted = $formatter->formatShort('SE556819238801');
        $this->assertEquals('556819238801', $formatted);
    }

    public function testFormatShortWithoutSEPrefix()
    {
        $formatter = new SE();
        $formatted = $formatter->formatShort('556819238801');
        $this->assertEquals('556819238801', $formatted);
    }

    public function testFormatShortWithInvalidTrailing()
    {
        $formatter = new SE();
        $formatted = $formatter->formatShort('SE556819238800');
        $this->assertEquals('', $formatted);
        $formatted = $formatter->formatShort('SE556819238896');
        $this->assertEquals('', $formatted);
    }

    public function testFormatShortWithInvalidChecksum()
    {
        $formatter = new SE();
        $formatted = $formatter->formatShort('SE556819248801');
        $this->assertEquals('', $formatted);
    }
}
