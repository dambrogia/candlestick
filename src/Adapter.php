<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\AdapterException;

class Adapter
{
    public function __construct(array $map = [])
    {
        $this->map = empty($map) ? $this->getDefaultMap() : $map;
    }

    /**
     * Get the default map.
     * @return array
     */
    protected function getDefaultMap(): array
    {
        return [
            'open' => 'open',
            'high' => 'high',
            'low' => 'low',
            'close' => 'close',
            'volume' => 'volume',
            'date' => 'date',
        ];
    }

    /**
     * Minor helper function to cache the keys returned from the map.
     *
     * @return array
     */
    protected function getMapKeys(): array
    {
        return $this->mapKeys = empty($this->mapKeys)
            ? array_keys($this->getDefaultMap())
            : $this->mapKeys;
    }

    /**
     * Adapt every index in the array.
     * @param array $items
     * @return array
     */
    public function map(array $items): array
    {
        return array_map(function (array $candle) {
            return $this->adapt($candle);
        }, $items);
    }

    /**
     * Get a mapped a field value within the candle based on the key provided.
     *
     * @param string $key
     * @return void
     */
    protected function getMapped(string $key)
    {
        if (! isset($this->map[$key])) {
            throw new AdapterException('Missing mapped field: ' . $key);
        }

        return $this->map[$key];
    }

    /**
     * Adapt the given fields in the array to the expected fields for a
     * candlestick. Return the candlestick.
     * @param array $candle
     * @return Candlestick
     * @throws AdapterException
     */
    public function adapt(array $candle): Candlestick
    {
        $values = array_map(function ($field) use ($candle) {
            $mapped = $this->getMapped($field);
            return is_callable($mapped) ? $mapped($candle) : $candle[$mapped];
        }, $this->getMapKeys());

        return new Candlestick(...$values);
    }
}
