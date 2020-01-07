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
            new Candlestick(1, 2, 3, 4, 100, time())
        );
    }

    public function testGetters(): void
    {
        $t = time();
        $candle = new Candlestick(1, 2, 3, 4, 100, $t);

        $this->assertEquals(1, $candle->open());
        $this->assertEquals(2, $candle->high());
        $this->assertEquals(3, $candle->low());
        $this->assertEquals(4, $candle->close());
        $this->assertEquals(100, $candle->volume());
        $this->assertEquals($t, $candle->date());
    }

    public function testMagicGet(): void
    {
        $t = time();
        $candle = new Candlestick(1, 2, 3, 4, 100, $t);

        $this->assertEquals(1, $candle->o);
        $this->assertEquals(2, $candle->h);
        $this->assertEquals(3, $candle->l);
        $this->assertEquals(4, $candle->c);
        $this->assertEquals(100, $candle->v);
        $this->assertEquals($t, $candle->d);

        // Invalid Value.
        $this->expectException(CandlestickException::class);
        $candle->z;
    }
}
