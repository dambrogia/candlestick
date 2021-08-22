<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CollectionException;
use JsonSerializable;

class Collection implements JsonSerializable
{
    protected $items = [];
    protected $adapter;

    public function __construct(array $items = [], ?Adapter $adapter = null)
    {
        $this->adapter = is_null($adapter) ? new Adapter() : $adapter;
        $this->setItems($items);
    }

    /**
     * Used to serialize the collection.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function (Candlestick $candle) {
           return $candle->jsonSerialize();
        }, $this->getItems());
    }

    /**
     * This will create a new collection with the default map return those items
     * from the new collection into the existing map to retain the original map.
     *
     * @param string $json
     * @return self
     */
    public function jsonUnserialize(string $json)
    {
        $arr = json_decode($json, true);
        $collection = new static($arr, null);
        return $this->setItems($collection->getItems(), true);
    }

    /**
     * Set the items in the collection. The adapter will map fields which
     * naturally forces validation of the structure of the collection. Passing
     * the adapter is available with the quick option for performance, but is
     * strongrly recommended and required for adapting/mapping fields.
     * @param array $items
     * @param boolean $quick
     * @return self
     */
    public function setItems(array $items, bool $quick = false): self
    {
        $this->items = $quick ? $items : $this->adapter->map($items);
        return $this;
    }

    /**
     * Return the items in the collection.
     * @return Candlestick[]
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
     * Get all the volume values in the collection.
     * @return array
     */
    public function volumes(): array
    {
        return array_map(function ($candle) {
            return $candle->volume();
        }, $this->items);
    }

    /**
     * Get all the date values in the collection.
     * @return array
     */
    public function dates(): array
    {
        return array_map(function ($candle) {
            return $candle->dates();
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
    public function range(int $start, int $stop = 0): self
    {
        $stop = $stop == 0 ? $this->size() - 1 : $stop;
        $start = $start < 0 ? $this->size() + $start : $start;

        if ($start > $stop || $stop >= $this->size()) {
            throw new CollectionException('Invalid range paramters.');
        }

        for ($i = $start; $i <= $stop; $i++) {
            $items[] = $this->get($i);
        }

        $new = (new self([], $this->adapter));
        $new->setItems($items, true);
        return $new;
    }

    /**
     * Filter the candles based on a provided function. If the function returns
     * true, the existing candle will remain in the collection. If the function
     * returns false, the exist candle will be removed from the collection.
     *
     * @param callable $fn
     * @return self
     */
    public function filter(callable $fn): self
    {
        $new = [];

        foreach ($this->getItems() as $candle) {
            if ($fn($candle)) {
                $new[] = $candle;
            }
        }

        return $this->setItems($new, true);
    }
}
