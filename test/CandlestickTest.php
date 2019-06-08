<?php

namespace Dambrogia\CandlestickTest;

use PHPUnit\Framework\TestCase;
use Dambrogia\Candlestick\Candlestick;
use Dambrogia\Candlestick\Concern\CandlestickException;

final class CandlestickTest extends TestCase
{
    public function testCanCreate(): void
    {
        $this->assertInstanceOf(
            Candlestick::class,
            new Candlestick(1, 2, 3, 4, '01/01/2018')
        );
    }

    public function testGetters(): void
    {
        $candle = new Candlestick(1, 2, 3, 4, '01/01/2018');

        $this->assertEquals(1, $candle->getOpen());
        $this->assertEquals(2, $candle->getHigh());
        $this->assertEquals(3, $candle->getLow());
        $this->assertEquals(4, $candle->getClose());
    }

    public function testMagicGet(): void
    {
        $candle = new Candlestick(1, 2, 3, 4, '01/01/2018');

        $this->assertEquals(1, $candle->open);
        $this->assertEquals(2, $candle->high);
        $this->assertEquals(3, $candle->low);
        $this->assertEquals(4, $candle->close);
    }

    public function testException(): void
    {
        $this->expectException(CandlestickException::class);

        $candle = new Candlestick(1, 2, 3, 4, '01/01/2018');
        $candle->invalidProperty;
    }
}
