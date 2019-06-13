<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CollectionException;

class Collection
{
    protected $items = [];
    protected $adapter;
    protected $candleMethods = [
        '2crows', '3blackcrows', '3inside', '3linestrike', '3outside',
        '3starsinsouth', '3whitesoldiers', 'abandonedbaby', 'advanceblock',
        'belthold', 'breakaway', 'closingmarubozu', 'concealbabyswall',
        'counterattack','darkcloudcover','doji', 'dojistar', 'dragonflydoji',
        'engulfing', 'eveningdojistar', 'eveningstar', 'gapsidesidewhite',
        'gravestonedoji', 'hammer', 'hangingman', 'harami', 'haramicross',
        'highwave', 'hikkake', 'hikkakemod', 'homingpigeon', 'identical3crows',
        'inneck', 'invertedhammer', 'kicking', 'kickingbylength', 'ladderbottom',
        'longleggeddoji', 'longline', 'marubozu', 'matchinglow', 'mathold',
        'morningdojistar', 'morningstar', 'onneck', 'piercing', 'rickshawman',
        'risefall3methods', 'separatinglines', 'shootingstar', 'shortline',
        'spinningtop', 'stalledpattern', 'sticksandwich', 'takuri', 'tasukigap',
        'thrusting', 'tristar', 'unique3river', 'upsidegap2crows',
        'xsidegap3methods',
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
}
