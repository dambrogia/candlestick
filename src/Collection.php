<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CollectionException;

class Collection
{
    protected $items = [];
    protected $adapter;
    protected $candleMethods = [
        'trader_cdl2crows',
        'trader_cdl3blackcrows',
        'trader_cdl3inside',
        'trader_cdl3linestrike',
        'trader_cdl3outside',
        'trader_cdl3starsinsouth',
        'trader_cdl3whitesoldiers',
        'trader_cdlabandonedbaby',
        'trader_cdladvanceblock',
        'trader_cdlbelthold',
        'trader_cdlbreakaway',
        'trader_cdlclosingmarubozu',
        'trader_cdlconcealbabyswall',
        'trader_cdlcounterattack',
        'trader_cdldarkcloudcover',
        'trader_cdldoji',
        'trader_cdldojistar',
        'trader_cdldragonflydoji',
        'trader_cdlengulfing',
        'trader_cdleveningdojistar',
        'trader_cdleveningstar',
        'trader_cdlgapsidesidewhite',
        'trader_cdlgravestonedoji',
        'trader_cdlhammer',
        'trader_cdlhangingman',
        'trader_cdlharami',
        'trader_cdlharamicross',
        'trader_cdlhighwave',
        'trader_cdlhikkake',
        'trader_cdlhikkakemod',
        'trader_cdlhomingpigeon',
        'trader_cdlidentical3crows',
        'trader_cdlinneck',
        'trader_cdlinvertedhammer',
        'trader_cdlkicking',
        'trader_cdlkickingbylength',
        'trader_cdlladderbottom',
        'trader_cdllongleggeddoji',
        'trader_cdllongline',
        'trader_cdlmarubozu',
        'trader_cdlmatchinglow',
        'trader_cdlmathold',
        'trader_cdlmorningdojistar',
        'trader_cdlmorningstar',
        'trader_cdlonneck',
        'trader_cdlpiercing',
        'trader_cdlrickshawman',
        'trader_cdlrisefall3methods',
        'trader_cdlseparatinglines',
        'trader_cdlshootingstar',
        'trader_cdlshortline',
        'trader_cdlspinningtop',
        'trader_cdlstalledpattern',
        'trader_cdlsticksandwich',
        'trader_cdltakuri',
        'trader_cdltasukigap',
        'trader_cdlthrusting',
        'trader_cdltristar',
        'trader_cdlunique3river',
        'trader_cdlupsidegap2crows',
        'trader_cdlxsidegap3methods',
    ];

    public function __construct(array $items = [], Adapter $adapter = null)
    {
        $this->adapter = is_null($adapter) ? new Adapter() : $adapter;
        $this->setItems($items);
    }

    /**
     * Set the items in the collection. The adapter will map fields which
     * naturally forces validation of the structure of the collection. Passing
     * the adapter is available with the quick option for performance, but is
     * strongrly recommended and required for adapting/mapping fields.
     * @param array $items
     * @param boolean $quick
     * @return void
     */
    public function setItems(array $items, bool $quick = false): void
    {
        $this->items = $quick ? $items : $this->adapter->map($items);
    }

    /**
     * Return the items in the collection.
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Return an index within the collection.
     * @param integer $index
     * @return Candlestick
     */
    public function get(int $index): Candlestick
    {
        return $this->items[$index];
    }

    /**
     * Matches the grouped format of the trader_* functions.
     *  ex: $grouped = $collection->getGrouped();
     *      $result = trader_cdldoji(...$grouped);
     * @return array
     */
    public function getGrouped(): array
    {
        $open = [];
        $high = [];
        $low = [];
        $close = [];

        foreach ($this->items as $candle) {
            $open[] = $candle->getOpen();
            $high[] = $candle->getHigh();
            $low[] = $candle->getLow();
            $close[] = $candle->getClose();
        }

        return [ $open, $high, $low, $close ];
    }

    /**
     * Get all the open values in the collection.
     * @return array
     */
    public function opens(): array
    {
        return array_map(function ($candle) {
            return $candle->getOpen();
        }, $this->items);
    }

    /**
     * Get all the high values in the collection.
     * @return array
     */
    public function highs(): array
    {
        return array_map(function ($candle) {
            return $candle->getHigh();
        }, $this->items);
    }

    /**
     * Get all the low values in the collection.
     * @return array
     */
    public function lows(): array
    {
        return array_map(function ($candle) {
            return $candle->getLow();
        }, $this->items);
    }

    /**
     * Get all the close values in the collection.
     * @return array
     */
    public function closes(): array
    {
        return array_map(function ($candle) {
            return $candle->getClose();
        }, $this->items);
    }

    /**
     * Get the amount of items in the collection.
     * @return int
     */
    public function size(): int
    {
        return count($this->items);
    }

    /**
     * Get a new collection from a range of items within the collection.
     * @param int $start
     * @param int $stop
     * @return self
     */
    public function range(int $start, int $stop): self
    {
        if ($start > $stop || $start < 0 || $stop >= $this->size()) {
            throw new CollectionException('Invalid range paramters.');
        }

        $items = [];

        for ($i = $start; $i <= $stop; $i++) {
            $items[] = $this->get($i);
        }

        $new = (new self([], $this->adapter));
        $new->setItems($items, true);
        return $new;
    }

    /**
     * Allow for trader_* functions.
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (\in_array($name, $this->candleMethods)) {
            return $name(...$this->getGrouped());
        }

        throw new CollectionException('Invalid method name.');
    }

    /**
     * Get the available candle methods to call.
     * @return array
     */
    public function getCandleMethods(): array
    {
        return $this->candleMethods;
    }
}
