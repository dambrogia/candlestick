<?php

namespace Dambrogia\Candlestick;

use Dambrogia\Candlestick\Concern\CandlestickException;

class Candlestick
{
    protected $open;
    protected $high;
    protected $low;
    protected $close;
    protected $date;

    protected $readable = [ 'open', 'high', 'low', 'close', 'date' ];

    /**
     * Create a new candle. Order of paramters is intentionally the same order
     * as the trader_* functions in the PHP's PECL package.
     * @param float $open
     * @param float $high
     * @param float $low
     * @param float $close
     * @param string $date (optional)
     */
    public function __construct($open, $high, $low, $close, $date = '')
    {
        $this->open = (float) $open;
        $this->high = (float) $high;
        $this->low = (float) $low;
        $this->close = (float) $close;
        $this->date = (string) $date;
    }

    /**
     * Allow for reading $readable properties, rather than using getter method.
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (\in_array($name, $this->readable)) {
            return $this->{$name};
        }

        throw new CandlestickException('Missing property: ' . $name);
    }

    /**
     * Getter for class property: open.
     * @return float
     */
    public function getOpen(): float
    {
        return $this->open;
    }

    /**
     * Getter for class property: high.
     * @return float
     */
    public function getHigh(): float
    {
        return $this->high;
    }

    /**
     * Getter for class property: low.
     * @return float
     */
    public function getLow(): float
    {
        return $this->low;
    }

    /**
     * Getter for class property: close.
     * @return float
     */
    public function getClose(): float
    {
        return $this->close;
    }

    /**
     * Getter for class property: close.
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }
}
