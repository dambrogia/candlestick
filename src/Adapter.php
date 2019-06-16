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
            'date' => 'date',
        ];
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
     * Adapt the given fields in the array to the expected fields for a
     * candlestick. Return the candlestick.
     * @param array $candle
     * @return Candlestick
     * @throws AdapterException
     */
    public function adapt(array $candle): Candlestick
    {
        // The order here matters, needs to match Candlestick constructor order.
        $fields = array_keys($this->getDefaultMap());
        $values = [];

        foreach ($fields as $field) {
            $mapped = $this->map[$field];

            if (is_callable($mapped)) {
                $values[] = $mapped($candle);
            } elseif ($field === 'date' && $mapped === false) {
                // Allow for skipping date field.
                $values[] = '';
            } elseif (! isset($candle[$mapped])) {
                throw new AdapterException('Missing mapped field: ' . $mapped);
            } else {
                $values[] = $candle[$mapped];
            }
        }

        return new Candlestick(...$values);
    }
}
