<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CollectionException;

class Collection
{
    protected $items = [];
    protected $adapter;

    public function __construct(array $items = [], ?Adapter $adapter = null)
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
    public function grouped(): array
    {
        $open = [];
        $high = [];
        $low = [];
        $close = [];

        foreach ($this->items as $candle) {
            $open[] = $candle->open();
            $high[] = $candle->high();
            $low[] = $candle->low();
            $close[] = $candle->close();
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
            return $candle->open();
        }, $this->items);
    }

    /**
     * Get all the high values in the collection.
     * @return array
     */
    public function highs(): array
    {
        return array_map(function ($candle) {
            return $candle->high();
        }, $this->items);
    }

    /**
     * Get all the low values in the collection.
     * @return array
     */
    public function lows(): array
    {
        return array_map(function ($candle) {
            return $candle->low();
        }, $this->items);
    }

    /**
     * Get all the close values in the collection.
     * @return array
     */
    public function closes(): array
    {
        return array_map(function ($candle) {
            return $candle->close();
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
     * Get the first item in the collection.
     * @return Candlestick
     */
    public function first(): Candlestick
    {
        return $this->get(0);
    }

    /**
     * Get the last item in the collection.
     * @return Candlestick
     */
    public function last(): Candlestick
    {
        return $this->get($this->size() - 1);
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
