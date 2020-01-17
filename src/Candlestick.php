<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CandlestickException;

class Candlestick
{
    /** @var float */
    protected $open;

    /** @var float */
    protected $high;

    /** @var float */
    protected $low;

    /** @var float */
    protected $close;

    /** @var int */
    protected $volume;

    /** @var int */
    protected $date;

    /**
     * Create a new candle. Order of paramters is intentionally the same order
     * as the trader_* functions in the PHP's PECL package.
     * @param float $open
     * @param float $high
     * @param float $low
     * @param float $close
     * @param int $volume
     * @param int $date
     */
    public function __construct($open, $high, $low, $close, $volume, $date)
    {
        $this->open = (float) $open;
        $this->high = (float) $high;
        $this->low = (float) $low;
        $this->close = (float) $close;
        $this->volume = (int) $volume;
        $this->date = (int) $date;
    }

    /**
     * Getter for class property: open.
     * @return float
     */
    public function open(): float
    {
        return $this->open;
    }

    /**
     * Getter for class property: high.
     * @return float
     */
    public function high(): float
    {
        return $this->high;
    }

    /**
     * Getter for class property: low.
     * @return float
     */
    public function low(): float
    {
        return $this->low;
    }

    /**
     * Getter for class property: close.
     * @return float
     */
    public function close(): float
    {
        return $this->close;
    }

    /**
     * Getter for class property: volume.
     * @return int
     */
    public function volume(): int
    {
        return $this->volume;
    }

    /**
     * Getter for class property: date.
     * @return int
     */
    public function date(): int
    {
        return $this->date;
    }

    /**
     * Transform the candle to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'o' => $this->open(),
            'h' => $this->high(),
            'l' => $this->low(),
            'c' => $this->close(),
            'v' => $this->volume(),
            'd' => $this->date()
        ];
    }

    /**
     * Transform this object to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * Magic method for a shorthand retrieval of an "ohlcvd" value.
     *
     * @return mixed
     */
    public function __get($letter)
    {
        $array = $this->toArray();

        if (! isset($array[$letter])) {
            throw new CandlestickException('Invalid property: ' . $letter);
        }

        return $array[$letter];
    }
}
