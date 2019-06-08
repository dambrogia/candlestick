<?php

namespace Dambrogia\CandlestickTest;

use PHPUnit\Framework\TestCase;
use Dambrogia\Candlestick\Adapter;
use Dambrogia\Candlestick\Candlestick;

final class AdapterTest extends TestCase
{
    use DataTrait;

    public function testCanCreate(): void
    {
        $this->assertInstanceOf(Adapter::class, new Adapter);
    }

    /**
     * Data is taken from:
     * https://api.tiingo.com/tiingo/daily/goog/prices?startDate=2019-01-02&token=<token>
     * @return void
     */
    public function testCanMap(): void
    {
        $defaultMap = [
            'open' => 'open',
            'high' => 'high',
            'low' => 'low',
            'close' => 'close',
            'date' => 'date',
        ];

        $capsMap = [
            'open' => 'OPEN',
            'high' => 'HIGH',
            'low' => 'LOW',
            'close' => 'CLOSE',
            'date' => 'DATE',
        ];

        $defaultAdapter = new Adapter($defaultMap);
        $capsAdapter = new Adapter($capsMap);

        $candles = $this->getDataArray();
        $capsCandles = $this->getDataArrayToUpper();

        $candle = $defaultAdapter->adapt($candles[0]);
        $capsCandle = $capsAdapter->adapt($capsCandles[0]);

        $this->assertInstanceOf(Candlestick::class, $candle);
        $this->assertInstanceOf(Candlestick::class, $capsCandle);
    }
}
